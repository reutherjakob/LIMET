<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

$sql = "SELECT n.idtabelle_notizen, n.Datum, n.Kategorie, n.User, n.Notiz_bearbeitet, 
               r.Raumnr, r.Raumbezeichnung, r.`Raumbereich Nutzer`
        FROM tabelle_räume r
        INNER JOIN tabelle_notizen n ON r.idTABELLE_Räume = n.tabelle_räume_idTABELLE_Räume
        WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
        ORDER BY n.Datum DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();

echo "<table class='table table-striped table-bordered table-condensed' id='tableProjectNotices'>
    <thead>
        <tr>
            <th>ID</th>
            <th>Datum</th>
            <th>Status</th>
            <th>Kategorie</th>
            <th>User</th>
            <th>Raumbereich Nutzer</th>
            <th>Raumnr</th>
            <th>Raumbezeichnung</th>
        </tr>
    </thead>
    <tbody>";

while ($row = $result->fetch_assoc()) {
    $status = $row["Notiz_bearbeitet"] == 0 ? "Offen" : ($row["Notiz_bearbeitet"] == 1 ? "Bearbeitet" : "Info");
    echo "<tr>
        <td>" . htmlspecialchars($row["idtabelle_notizen"] ?? '') . "</td>
        <td>" . htmlspecialchars($row["Datum"] ?? '') . "</td>
        <td>" . htmlspecialchars($status) . "</td>
        <td>" . htmlspecialchars($row["Kategorie"] ?? '') . "</td>
        <td>" . htmlspecialchars($row["User"] ?? '') . "</td>
        <td>" . htmlspecialchars($row["Raumbereich Nutzer"] ?? '') . "</td>
        <td>" . htmlspecialchars($row["Raumnr"] ?? '') . "</td>
        <td>" . htmlspecialchars($row["Raumbezeichnung"] ?? '') . "</td>
    </tr>";
}
echo "</tbody></table>";

$stmt->close();
$mysqli->close();
?>
<script>

    $(document).ready(function(){
        $('#tableProjectNotices').DataTable( {
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                }
            ],
            "paging": false,
            "searching": false,
            "info": false,
            "order": [[ 1, "desc" ]],
            'language': {'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json'},
            "scrollY":        '20vh',
            "scrollCollapse": true,
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                if ( aData[2] == "Offen" )
                {
                    $('td', nRow).css('background-color', 'LightCoral');
                }
                else if ( aData[2] == "Bearbeitet" )
                {
                    $('td', nRow).css('background-color', 'LightGreen');
                }
                else if ( aData[2] == "Info" )
                {
                    $('td', nRow).css('background-color', 'DeepSkyBlue');
                }

            }
        } );

        // CLICK TABELLE Geräte IN DB
        var table1 = $('#tableProjectNotices').DataTable();

        $('#tableProjectNotices tbody').on( 'click', 'tr', function () {

            if ( $(this).hasClass('info') ) {
                //$(this).removeClass('info');
            }
            else {
                table1.$('tr.info').removeClass('info');
                $(this).addClass('info');
                var noticeID = table1.row( $(this) ).data()[0];

                $.ajax({
                    url : 'getNoticeData.php',
                    data:{'noticeID':noticeID,"newNoticeButton":"0"},
                    type: 'GET',
                    success: function(data){
                        $('#addNotice1').html(data);
                    }
                });

            }
        });
    });</script>


