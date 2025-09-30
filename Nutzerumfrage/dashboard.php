<?php

global $mysqli;
require_once "../Nutzerlogin/_utils.php";

if (!function_exists('loadEnv')) {
    include "../Nutzerlogin/db.php";
}

init_page(["internal_rb_user", "spargefeld_ext_users"]);
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');

//  $sql = "SELECT idTABELLE_Räume AS raum_id, Raumbezeichnung AS raumname, Raumnr AS raumnummer, `Raumbereich Nutzer` AS bereich, Nutzfläche, Geschoss
//      FROM tabelle_räume WHERE tabelle_projekte_idTABELLE_Projekte = 1";
//
//  if ($result = $mysqli->query($sql)) {
//      $raeume = [];
//      while ($row = $result->fetch_assoc()) {
//          $raeume[] = $row;
//      }
//      $result->free();
//  } else {
//      die("Fehler in der Abfrage: " . $mysqli->error);
//  }
//
//  $mysqli->close();


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

<body class="p-3">
<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="row">
                        <div class="col-9">
                            <strong>Räume des Projekts </strong>
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
                            <th><i class="fas fa-layer-group"></i></th>
                        </tr>
                        </thead>
                        <!---tbody>
                        <?php foreach ($raeume as $raum): ?>
                            <tr data-id="<?= htmlspecialchars($raum['raum_id']) ?>"
                                data-name="<?= htmlspecialchars($raum['raumname']) ?>">
                                <td><?= htmlspecialchars($raum['raumnummer']) ?></td>
                                <td><?= htmlspecialchars($raum['raumname']) ?></td>
                                <td><?= htmlspecialchars($raum['bereich']) ?></td>
                                <td><?= htmlspecialchars($raum['raum_id']) ?></td>
                                <td><?= htmlspecialchars($raum['Nutzfläche']) ?></td>
                                <td><?= htmlspecialchars($raum['Geschoss']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody--->
                    </table>
                </div>
            </div>
        </div>
        <!-- Rechte Spalte: Formular -->
        <div class="col-6">
            <div class="card">
                <div class="card-header bg-light ">
                    <div class="row">
                        <div class="col-9">
                            Raumanforderung erfassen
                        </div>
                        <div class="col-3 d-flex justify-content-end" id="cardHeaderRaumanforderungen">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="formContainer">
                        <p>Bitte wählen Sie einen Raum aus der linken Tabelle aus.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        $.get("navbar.html", function (data) {
            $("#limet-navbar").html(data);
            let Username = "<?php echo $_SESSION['username']?>";
            $("#navbar-username").text(capitalizeFirstLetter(Username));
        });

        $('#raeumeTable').DataTable({
            paging: true,           // Enable or disable pagination (true/false)
            pageLength: 30,         // Number of rows per page
            lengthChange: true,     // Allow the user to change the number of rows shown
            searching: true,        // Enable or disable the search/filter input
            ordering: true,         // Enable or disable column sorting
            order: [[0, 'asc']],    // Initial ordering: first column ascending
            info: true,             // Show table info (e.g., "Showing 1 to 10 of 50 entries")
            responsive: true,       // Enable responsive design for smaller screens
            autoWidth: false,       // Disable automatic column width calculation
            language: {             // Customize language strings (example: German)
                search: "Suchen:",
                lengthMenu: "Zeige _MENU_ Einträge",
                info: "Zeige _START_ bis _END_ von _TOTAL_ Einträgen",
                paginate: {
                    first: "Erster",
                    last: "Letzter",
                    next: "Nächster",
                    previous: "Vorheriger"
                },
                searchPlaceholder: "Raumsuche..."
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
            let raumId = $(this).data("id");
            $.ajax({
                url: "spargelfeld_nutzerabfrage_0.php",
                method: "GET",
                data: {raumid: raumId},
                success: function (response) {
                    $("#formContainer").html(response);

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
<?php
