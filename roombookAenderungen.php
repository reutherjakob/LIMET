<?php
include '_utils.php';
init_page_serversides("", "x");
$conn = utils_connect_sql();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>Raumänderungen</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/bs5/dt-2.2.1/datatables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
</head>
<body>

<div id="limet-navbar"></div>
<div class="container-fluid mt-4">

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4>Räume</h4>
        </div>
        <div class="card-body">
            <table id="roomTable" class="table table-striped" style="width:100%">
                <thead>
                <tr>
                    <th>Raumnr</th>
                    <th>Raumbezeichnung</th>
                    <th>Letzte Änderung</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $rooms = $conn->query("
                        SELECT r.idTABELLE_Räume, r.Raumnr, r.Raumbezeichnung, 
                               MAX(a.Timestamp) AS last_change
                        FROM tabelle_räume r
                        LEFT JOIN tabelle_raeume_aenderungen a ON r.idTABELLE_Räume = a.raum_id
                        WHERE r.tabelle_projekte_idTABELLE_Projekte = {$_SESSION["projectID"]}
                        GROUP BY r.idTABELLE_Räume
                    ");

                while($room = $rooms->fetch_assoc()):
                    ?>
                    <tr data-room-id="<?= $room['idTABELLE_Räume'] ?>">
                        <td><?= htmlspecialchars($room['Raumnr']) ?></td>
                        <td><?= htmlspecialchars($room['Raumbezeichnung']) ?></td>
                        <td><?= $room['last_change'] ? date('d.m.Y H:i', strtotime($room['last_change'])) : 'Keine Änderungen' ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Changes Card -->
    <div class="card collapse" id="changesCard">
        <div class="card-header bg-secondary text-white">
            <h4>Änderungshistorie</h4>
        </div>
        <div class="card-body" id="changesContent">
            <!-- Changes will be loaded here -->
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-2.2.1/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        const roomTable = $('#roomTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/German.json'
            },
            order: [[2, 'desc']]
        });

        $('#roomTable tbody').on('click', 'tr', function() {
            const roomId = $(this).data('room-id');
            loadRoomChanges(roomId);
            $('#changesCard').collapse('show');
        });

        function loadRoomChanges(roomId) {
            $('#changesContent').html('<div class="text-center my-4"><div class="spinner-border" role="status"></div></div>');

            $.ajax({
                url: 'get_room_changes.php',
                data: { roomId: roomId },
                success: function(response) {
                    $('#changesContent').html(response);
                },
                error: function() {
                    $('#changesContent').html('<div class="alert alert-danger">Fehler beim Laden der Änderungen</div>');
                }
            });
        }
    });
</script>
</body>
</html>
