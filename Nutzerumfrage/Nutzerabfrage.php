<?php
global $mysqli;
require_once "../Nutzerlogin/_utils.php";
if (!function_exists('loadEnv')) {
    include "../Nutzerlogin/db.php";
}

$role = init_page(["internal_rb_user", "spargefeld_ext_users", "spargefeld_admin"]);
$user_name = $_SESSION["user_name"];
$projekt_id = 95;

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');


if ($role === "internal_rb_user" || $role === "spargefeld_admin") {
    $sql = "SELECT idTABELLE_Räume AS raum_id, Raumbezeichnung AS raumname, Raumnr AS raumnummer, `Raumbereich Nutzer` AS bereich, Nutzfläche, Bauabschnitt
            FROM tabelle_räume
            WHERE tabelle_projekte_idTABELLE_Projekte = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $projekt_id);
    } else {
        die("Fehler in der Abfrage: " . $mysqli->error);
    }
} else {
    if (str_contains($user_name, 'TIF')) {
        $sql = "SELECT idTABELLE_Räume AS raum_id, Raumbezeichnung AS raumname, Raumnr AS raumnummer,
                   `Raumbereich Nutzer` AS bereich, Nutzfläche, Bauabschnitt
            FROM tabelle_räume
            WHERE tabelle_projekte_idTABELLE_Projekte = ?
              AND (`Raumbereich Nutzer` LIKE ? OR `Raumbereich Nutzer` LIKE 'Allgemein_H_TIF%')";
    } elseif (str_contains($user_name, 'LSV')) {
        $sql = "SELECT idTABELLE_Räume AS raum_id, Raumbezeichnung AS raumname, Raumnr AS raumnummer,
                   `Raumbereich Nutzer` AS bereich, Nutzfläche, Bauabschnitt
            FROM tabelle_räume
            WHERE tabelle_projekte_idTABELLE_Projekte = ?
              AND (`Raumbereich Nutzer` LIKE ? OR `Raumbereich Nutzer` LIKE 'Allgemein_H_LSV%')";
    } else {
        $sql = "SELECT idTABELLE_Räume AS raum_id, Raumbezeichnung AS raumname, Raumnr AS raumnummer,
                   `Raumbereich Nutzer` AS bereich, Nutzfläche, Bauabschnitt
            FROM tabelle_räume
            WHERE tabelle_projekte_idTABELLE_Projekte = ?
              AND `Raumbereich Nutzer` LIKE ?";
    }

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
$mysqli->close();

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
</head>
<style>     .rechtsbuendig {
        display: inline-block; /* Wichtig, damit text-align funktioniert */
        width: 150px; /* Beispielbreite, anpassen nach Bedarf */
        text-align: right;
        padding-right: 10px; /* Optional: Abstand zum Eingabefeld */
    }</style>


<div id="limet-navbar"></div>
<?php require_once "../Nutzerumfrage/_utils.php"; ?>


<body class="">
<div class="container-fluid">
    <div class="row">

        <div class="col-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="row">
                        <div class="col-9">
                            <strong>Labortechnisch relevante Räume </strong>
                            <button class="btn btn-sm btn-success "
                                    data-bs-toggle="popover"
                                    data-bs-content="Hier sind die als labortechnisch relevant eingestuften Räume ihres Bereiches des Raum und Funktionsprogrammes gelistet.">
                                <i class="fas fa-info-circle"></i>
                            </button>

                        </div>
                        <div class="col-3 d-flex justify-content-end" id="RDPTCH"></div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0" id="raeumeTable">
                        <thead class="table-light">
                        <tr>
                            <th>Nummer</th>
                            <th>Raumname</th>
                            <th>Bereich</th>
                            <th>ID</th>
                            <th>m²</th>
                            <th>Bauteil</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($raeume as $raum): ?>
                            <tr data-id="<?= htmlspecialchars($raum['raum_id']) ?>"
                                data-name="<?= htmlspecialchars($raum['raumname']) ?>">
                                <td><?= htmlspecialchars($raum['raumnummer']) ?></td>
                                <td><?= htmlspecialchars($raum['raumname']) ?></td>
                                <td><?= htmlspecialchars($raum['bereich']) ?></td>
                                <td><?= htmlspecialchars($raum['raum_id']) ?></td>
                                <td><?= htmlspecialchars($raum['Nutzfläche']) ?></td>
                                <td><?= htmlspecialchars($raum['Bauabschnitt']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Rechte Spalte: Formular -->
        <div class="col-8">
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
        });
        document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                const popover = bootstrap.Popover.getOrCreateInstance(el);

                if (popover._isShown()) {  // Bootstrap 5.3+ private method, alternatively check popover tip class
                    popover.hide();
                } else {
                    // Hide others (optional)
                    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(otherEl => {
                        if (otherEl !== el) {
                            bootstrap.Popover.getOrCreateInstance(otherEl).hide();
                        }
                    });
                    popover.show();
                }
            });
        });
    }

    $(document).ready(function () {
        reinitPopovers();

        $('#raeumeTable').DataTable({
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
                search: "Suchen:",
                lengthMenu: "Zeige _MENU_",
                info: "",
                paginate: {
                    first: "Erster",
                    last: "Letzter",
                    next: "Nächster",
                    previous: "Vorheriger"
                },
                searchPlaceholder: "Raumsuche...",
                select: {
                    rows: ""
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
                    targets: [3],
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
            }
        });


        $("#raeumeTable tbody tr").on("click", function () {
            $("#formContainer").html("");
            let roomID = $(this).data("id");
            let raumnr = $(this).find('td').eq(0).text().trim();
            let roomname = $(this).find('td').eq(1).text().trim();
            let raumbereich_nutzer = $(this).find('td').eq(2).text().trim();
            let ebene = $(this).find('td').eq(4).text().trim();
            let nf = $(this).find('td').eq(3).text().trim();

            // Log the values to console
            // console.log("raumId:", raumId);
            // console.log("raumnr:", raumnr);
            //  console.log("roomname:", roomname);
            // console.log("raumbereich_nutzer:", raumbereich_nutzer);
            // console.log("ebene:", ebene);
            // console.log("nf:", nf);

            $.ajax({
                url: "spargelfeld_nutzerabfrage_1.php",
                method: "POST",
                data: {roomID: roomID, roomname: roomname},
                success: function (response) {
                    $("#formContainer").html(response);

                    reinitPopovers(document.getElementById('formContainer'));

                    loadFormData(roomID);
                    $('[name="roomID"]').val(roomID);
                    $('[name="raumnr"]').val(raumnr);
                    $('[name="roomname"]').val(roomname);
                    $('[name="raumbereich_nutzer"]').val(raumbereich_nutzer);
                    $('[name="ebene"]').val(ebene);
                    $('[name="nf"]').val(nf);

                },
                error: function () {
                    $("#formContainer").html("<p class='text-danger'>Formular konnte nicht geladen werden.</p>");
                }
            });
        });


        $('#roomParameterForm').submit(function (e) {
            e.preventDefault(); // prevent normal form submit
            $.ajax({
                url: 'spargelfeld_save.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    alert(response.message);
                    if (response.status === 'success') {
                    }
                },
                error: function () {
                    alert('Error saving room data.');
                }
            });
        });
    });
</script>
</body>
</html>
