<?php
global $mysqli, $labortypen;
require_once "../Nutzerlogin/_utils.php";
if (!function_exists('loadEnv')) {
    include "../Nutzerlogin/db.php";
}
require_once "../Nutzerumfrage/raumtypen.php"; // lädt $labortypen

require_once "../Nutzerlogin/csrf.php";
$role = init_page(["internal_rb_user", "spargelfeld_ext_user", "spargelfeld_admin", "spargelfeld_view"]);
$user_name = $_SESSION["user_name"];
$projekt_id = 95;

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');


if ($role === "internal_rb_user" || $role === "spargelfeld_admin" || $role === "spargelfeld_view") {
    $sql = "SELECT idTABELLE_Räume AS raum_id, 
            Raumbezeichnung AS raumname, 
            Raumnr AS raumnummer, 
            `Raumbereich Nutzer` AS bereich, 
            Nutzfläche, 
            Bauabschnitt, 
            `Raumtyp BH`, Geschoss
            FROM tabelle_räume
            WHERE tabelle_projekte_idTABELLE_Projekte = ?
            AND  `Raumtyp BH` <> 34 
            AND  `Raumtyp BH` <> 35
            ";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $projekt_id);
    } else {
        die("Fehler in der Abfrage: " . $mysqli->error);
    }
} else {
    $sql = "SELECT idTABELLE_Räume AS raum_id, Raumbezeichnung AS raumname, Raumnr AS raumnummer,
                   `Raumbereich Nutzer` AS bereich, Nutzfläche,
                   Bauabschnitt, Geschoss,   `Raumtyp BH`
            FROM tabelle_räume
            WHERE tabelle_projekte_idTABELLE_Projekte = ?
                AND `Raumbereich Nutzer` LIKE ?
                AND  `Raumtyp BH` <> 34 
                AND  `Raumtyp BH` <> 35";

    if ($stmt = $mysqli->prepare($sql)) {
        $like_param = "%" . $user_name . "%";
        $stmt->bind_param("is", $projekt_id, $like_param);
    } else {
        die("Fehler in der Abfrage: " . $mysqli->error);
    }
}

$stmt->execute();
$result = $stmt->get_result();
$raeume = [];
while ($row = $result->fetch_assoc()) {
    $raeume[] = $row;
}
$stmt->close();


$savedRoomIDs = [];
$sqlSaved = "SELECT roomID FROM tabelle_room_requirements_from_user 
             WHERE roomID IN (SELECT idTABELLE_Räume FROM tabelle_räume WHERE tabelle_projekte_idTABELLE_Projekte = ?)";
if ($stmtSaved = $mysqli->prepare($sqlSaved)) {
    $stmtSaved->bind_param("i", $projekt_id);
    $stmtSaved->execute();
    $resSaved = $stmtSaved->get_result();
    while ($row = $resSaved->fetch_assoc()) {
        $savedRoomIDs[] = (int)$row['roomID'];
    }
    $stmtSaved->close();
}

$mysqli->close();


$labortyp_map = [];
foreach ($labortypen as $lt) {
    $labortyp_map[$lt['id']] = $lt['bezeichnung'];
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="icon" href="../Logo/iphone_favicon.png">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
</head>
<style>
    .rechtsbuendig {
        display: inline-block; /* Wichtig, damit text-align funktioniert */
        width: 150px; /* Beispielbreite, anpassen nach Bedarf */
        text-align: right;
        padding-right: 10px; /* Optional: Abstand zum Eingabefeld */
    }
</style>


<div id="limet-navbar"></div>
<?php require_once "../Nutzerumfrage/_utils.php"; ?>


<body class="">
<div class="container-fluid">
    <div class="row">

        <div class="col-5">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="row">
                        <div class="col-9">
                            <strong>Labortechnisch relevante Räume </strong>
                            <button class="btn btn-sm btn-success "
                                    data-bs-toggle="popover"
                                    data-bs-content="Hier sind die als labortechnisch relevant eingestuften Räume ihres Bereiches des RUF gelistet.
                                                     Darin sind auch die Raumkategorien festgelegt.
                                                     Die Abfragen sind an die Raumkategorien angepasst.">
                                <i class="fas fa-info-circle"></i>
                            </button>

                        </div>
                        <div class="col-3 d-flex justify-content-end" id="RDPTCH"></div>
                    </div>
                </div>
                <div class="card-body p-2">
                    <table class="table table-sm table-hover mb-0" id="raeumeTable">
                        <thead class="table-light">
                        <tr>
                            <th>Nummer</th>
                            <!---th>Raumname</th--->
                            <th>Raumkategorie</th>
                            <th>Bereich</th>
                            <th>Ebene</th>
                            <th>Bauteil</th>
                            <th>m²</th>

                            <th>ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($raeume as $raum): ?>
                            <tr data-id="<?= htmlspecialchars($raum['raum_id']) ?>"
                                data-name="<?= htmlspecialchars($raum['raumname']) ?>"
                                data-raumkategorie="<?= htmlspecialchars($raum['Raumtyp BH']) ?>"
                                data-raumkategoriename="<?= $labortyp_map[$raum['Raumtyp BH']] ?? $raum['Raumtyp BH'] ?>"
                                data-bauabschnitt="<?= htmlspecialchars($raum['Bauabschnitt']) ?>"
                                data-ebene="<?= htmlspecialchars($raum['Geschoss']) ?>"
                                data-nf="<?= htmlspecialchars($raum['Nutzfläche'] ?? '') ?>">

                                <td><?= htmlspecialchars($raum['raumnummer']) ?></td>
                                <td><?= htmlspecialchars($labortyp_map[$raum['Raumtyp BH']] ?? $raum['Raumtyp BH']) ?></td>
                                <!--td><?= htmlspecialchars($raum['raumname']) ?></td-->
                                <td><?= htmlspecialchars($raum['bereich']) ?></td>
                                <td><?= htmlspecialchars($raum['Geschoss']) ?></td>
                                <td><?= htmlspecialchars($raum['Bauabschnitt']) ?></td>
                                <td><?= htmlspecialchars($raum['Nutzfläche']) ?></td>

                                <td><?= htmlspecialchars($raum['raum_id']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Rechte Spalte: Formular -->
        <div class="col-7">
            <div class="card" id="formContainer">
                <div class="card-header"></div>
                <div class="card-body">
                    Wählen sie einen Raum aus.
                </div>
                <div class="card-foter"></div>

            </div>
        </div>
    </div>
</div>

<script>
    function reinitPopovers(container = document) {
        container.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
            bootstrap.Popover.getOrCreateInstance(el, {trigger: 'manual', container: 'body'});
            el.addEventListener('click', function (e) {
                e.preventDefault();
                const popover = bootstrap.Popover.getOrCreateInstance(el);
                if (popover._isShown()) {
                    popover.hide();
                } else {
                    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(otherEl => {
                        if (otherEl !== el) bootstrap.Popover.getOrCreateInstance(otherEl).hide();
                    });
                    popover.show();
                }
            });
        });
    }

    document.addEventListener('click', function (event) {
        const popoverElement = document.querySelector('.popover');
        if (!popoverElement) return;
        document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
            if (!el.contains(event.target) && !popoverElement.contains(event.target)) {
                bootstrap.Popover.getInstance(el)?.hide();
            }
        });
    });

    $(document).ready(function () {
        const csrfToken = "<?php echo csrf_token(); ?>";
        reinitPopovers();
        const savedRoomIDs = <?= json_encode($savedRoomIDs) ?>;
        const dt = $('#raeumeTable').DataTable({
            paging: true,           // Enable or disable pagination (true/false)
            pageLength: 25,         // Number of rows per page
            lengthChange: true,     // Allow the user to change the number of rows shown
            searching: true,        // Enable or disable the search/filter input
            ordering: true,         // Enable or disable column sorting
            order: [[0, 'asc']],    // Initial ordering: first column ascending
            info: true,             // Show table info (e.g., "Showing 1 to 10 of 50 entries")
            responsive: true,       // Enable responsive design for smaller screens
            autoWidth: false,       // Disable automatic column width calculation
            language: {             // Customize language strings (example: German)
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: "Suchen:",
                lengthMenu: "Zeige _MENU_",

                paginate: {
                    first: "Erster",
                    last: "Letzter",
                    next: "Nächster",
                    previous: "Vorheriger"
                },
                searchPlaceholder: "Raumsuche...",
                select: {
                    rows: "",
                    columns: "",
                    cells: ""
                }

            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: ["search", 'info'],
                bottomEnd: ['pageLength', 'paging']
            },
            columnDefs: [
                {
                    targets: [6],
                    visible: false,
                    searchable: false,
                    sortable: false
                }
            ],
            select: true,
            initComplete: function () {
                setTimeout(function () {
                    $('.dt-search input').addClass("btn btn-sm btn-outline-dark bg-white text-dark");
                    $('.dt-search label').remove();
                    $('.dt-search').children().removeClass('form-control form-control-sm').addClass("d-flex align-items-center").appendTo('#RDPTCH');
                }, 300);


                $('#raeumeTable tbody tr').each(function () {
                    const roomID = parseInt($(this).data('id'));
                    if (savedRoomIDs.includes(roomID)) {
                        $(this).css('background-color', '#d4edda');
                    }
                });



            }
        });

        dt.on('draw', function () {
            $('#raeumeTable tbody tr').each(function () {
                const roomID = parseInt($(this).data('id'));
                if (savedRoomIDs.includes(roomID)) {
                    $(this).css('background-color', '#d4edda');
                }
            });
        });

        $(document).off('change', '[data-select-comment-target]').on('change', '[data-select-comment-target]', function () {
            const targetName = $(this).data('select-comment-target');
            const wrap = $('#' + targetName + '_kommentar_wrap');
            if (!wrap.length) return;

            const defaultVal = String(wrap.data('default-val'));
            const selectedVal = String($(this).val());

            if (selectedVal !== defaultVal) {
                wrap.addClass('d-flex').slideDown(150);
            } else {
                wrap.slideUp(150, function () {
                    wrap.removeClass('d-flex');
                });
            }
        });


        $("#raeumeTable tbody").on("click", "tr", function () {
            document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
                const popover = bootstrap.Popover.getInstance(el);
                if (popover) popover.hide();
            });

            $("#formContainer").html("");
            let roomID = $(this).data("id");
            let raumnr = $(this).find('td').eq(0).text().trim();
            let roomname = $(this).data("name") || '';
            let raumbereich_nutzer = $(this).find('td').eq(2).text().trim();
            let raumkategorie = $(this).data("raumkategorie") || '';
            let raumkategoriename = $(this).data("raumkategoriename") || '';
            let bauabschnitt = $(this).data("bauabschnitt") || '';
            let ebene = $(this).data("ebene") || '';
            let nf = $(this).data("nf") || '';

            $.ajax({
                url: "spargelfeld_nutzerabfrage_1.php",
                method: "POST",
                data: {
                    roomID: roomID,
                    roomname: roomname,
                    raumkategorie: raumkategorie,
                    bauabschnitt: bauabschnitt,
                    ebene: ebene
                },
                success: function (response) {
                    $("#formContainer").html(response);

                    reinitPopovers(document.getElementById('formContainer'));
                    loadFormData(roomID);
                    $('[name="roomID"]').val(roomID);
                    $('[name="raumnr"]').val(raumnr);
                    $('[name="roomname"]').val(roomname);
                    $('[name="raumbereich_nutzer"]').val(raumbereich_nutzer);
                    $('[name="ebene"]').val(ebene);
                    $('[name="raumkategorieAbfrage"]').val(raumkategoriename);
                    $('[name="nf"]').val(nf);
                },
                error: function () {
                    $("#formContainer").html("<p class='text-danger'>Formular konnte nicht geladen werden.</p>");
                }
            });
        });


        $(document).on('submit', '#roomParameterForm', function (e) {
            e.preventDefault();
            const $form = $(this);   // <-- hierher
            const roomID = $form.find('[name="roomID"]').val();

            $.ajax({
                url: 'save_room_data.php', // war: spargelfeld_save.php
                type: 'POST',
                data: $(this).serialize() + '&csrf_token=' + encodeURIComponent(csrfToken),
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        // z.B. kurzes visuelles Feedback statt alert:
                        $('#saveBtn').text('✓ Gespeichert').removeClass('btn-outline-success').addClass('btn-success');
                        setTimeout(() => $('#saveBtn').text('Anforderungen speichern').addClass('far fa-save btn-success'), 2000);
                        // Im success-Handler von save_room_data (wo du aktuell z.B. eine Erfolgsmeldung zeigst):

                        $('#raeumeTable tbody tr[data-id="' + roomID + '"]').css('background-color', '#d4edda');
                    } else {
                        alert('Fehler: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    alert('Verbindungsfehler beim Speichern.\nStatus: ' + xhr.status + '\nAntwort: ' + xhr.responseText.substring(0, 300));
                }
            });
        });

        // loadFormData-Funktion (gehört hierher, nicht in spargelfeld_nutzerabfrage_1.php)
        function loadFormData(roomId) {
            $.get('load_room_data_userinputs.php', {roomId: roomId}, function (response) {
                if (response.error || response.newRoom) return;

                const data = response.data;
                const skipFields = ['raumnr', 'roomname', 'raumbereich_nutzer', 'ebene', 'nf'];

                for (const key in data) {
                    if (skipFields.includes(key)) continue;
                    const el = $('[name="' + key + '"]');
                    if (!el.length) continue;
                    const value = data[key];

                    const toggleBtn = $('#' + key + '_toggle');
                    if (toggleBtn.length) {
                        const btn = toggleBtn[0];
                        if (value === 'unbekannt') {
                            btn.textContent = 'unbekannt';
                            btn.className = 'btn btn-outline-secondary text-nowrap';
                            el.val('unbekannt');
                        } else if (value === 1 || value === '1') {
                            btn.textContent = 'Ja';
                            btn.className = 'btn btn-outline-success text-nowrap';
                            el.val('1');
                        } else {
                            btn.textContent = 'Nein';
                            btn.className = 'btn btn-outline-primary text-nowrap';
                            el.val('0');
                        }
                        const wrap = $('#' + key + '_kommentar_wrap');
                        if (wrap.length) {
                            const showIf = wrap.data('show-if');
                            const shouldShow = String(showIf) === String(value) && value !== 'unbekannt';
                            wrap.toggleClass('d-flex', shouldShow).toggle(shouldShow);
                        }
                        continue;
                    }
                    if (el.filter(':radio').length) {
                        el.filter('[value="' + value + '"]').prop('checked', true).trigger('change');
                        continue;
                    }
                    if (el.is(':checkbox')) {
                        el.prop('checked', value === 1 || value === '1');
                        continue;
                    }
                    if (el.is('select')) {
                        el.val(value).trigger('change');
                        continue;
                    }
                    el.val(value);
                }
            }, 'json');
        }


    }); // Ende document.ready


</script>
</body>
</html>
