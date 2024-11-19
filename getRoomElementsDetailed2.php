<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title>RoomElementsDetailed2</title>
</head>
<body>

<?php
include '_utils.php';
init_page_serversides("x", "x");
$mysqli = utils_connect_sql();

$sql = "SELECT Sum(`tabelle_räume_has_tabelle_elemente`.`Anzahl`*`tabelle_projekt_varianten_kosten`.`Kosten`) AS Summe_Neu
        FROM tabelle_räume_has_tabelle_elemente 
        INNER JOIN tabelle_projekt_varianten_kosten 
        ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
        AND (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)
        WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" . $_SESSION["roomID"] . ") 
        AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) 
        AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") 
        AND ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=1));";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
$number = $row["Summe_Neu"];
$formatter = new NumberFormatter('de_DE', NumberFormatter::CURRENCY);
$formattedNumber = $formatter->formatCurrency($number, 'EUR');
?>

<div class="d-inline-flex mw-100">
    <label for="kosten_neu">Raumkosten-Neu: </label>
    <input type="text" class="ml-2 form-control input-xs" id="kosten_neu" value="<?php echo $formattedNumber; ?>"
           disabled="disabled">
    <?php
    $sql = "SELECT Sum(`tabelle_räume_has_tabelle_elemente`.`Anzahl`*`tabelle_projekt_varianten_kosten`.`Kosten`) AS Summe_Bestand
            FROM tabelle_räume_has_tabelle_elemente 
            INNER JOIN tabelle_projekt_varianten_kosten 
            ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
            AND (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)
            WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" . $_SESSION["roomID"] . ") 
            AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) 
            AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") 
            AND ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=0));";
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    $number = $row["Summe_Bestand"];
    $formattedNumber = $formatter->formatCurrency($number, 'EUR');
    ?>
    <label class="" for="kosten_bestand">Raumkosten-Bestand: </label>
    <input type="text" class="ml-2 form-control input-xs" id="kosten_bestand"
           value="<?php echo $formattedNumber; ?>" disabled="disabled">

</div>
<?php

$sql = "SELECT tabelle_räume_has_tabelle_elemente.id,
tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete,
tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.Anzahl,
tabelle_elemente.ElementID, tabelle_elemente.Kurzbeschreibung As `Elementbeschreibung`, tabelle_varianten.Variante,
tabelle_elemente.Bezeichnung, tabelle_geraete.GeraeteID, tabelle_hersteller.Hersteller, tabelle_geraete.Typ,
tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort,
tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung,
tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete
FROM tabelle_varianten INNER JOIN (tabelle_hersteller RIGHT JOIN ((tabelle_räume_has_tabelle_elemente LEFT JOIN
tabelle_geraete ON tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete =
tabelle_geraete.idTABELLE_Geraete) INNER JOIN tabelle_elemente ON
tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON
tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON
tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" . $_SESSION["roomID"] . "))
ORDER BY tabelle_elemente.ElementID;";
$result = $mysqli->query($sql);
$mysqli->close();

echo "

<table class='table table-responsive table-striped table-bordered table-sm' id='tableRoomElements'>
    <thead>
    <tr>
        <th>ID</th>
        <th class='cols-md-1'>Stück</th>
        <th>Element</th>
        <th>Var.</th>
        <th>Best.</th>
        <th>Standort</th>
        <th>Verw.</th>
        <th>Kommentar</th>
    </tr>
    </thead>
    <tbody>";
//<!--	<th>Geräte ID</th>-->
while ($row = $result->fetch_assoc()) {
    echo "
    <tr>";
    echo "
        <td>" . $row["id"] . "</td>
        ";
    echo "
        <td>" . $row["Anzahl"] . "</td>
        ";
    echo "
        <td>" . $row["ElementID"] . " " . $row["Bezeichnung"] . "</td>
        ";
    echo "
        <td>" . $row["Variante"] . "</td>
        ";
    echo "
        <td>";
    if ($row["Neu/Bestand"] == 1) {
        echo "Nein";
    } else {
        echo "Ja";
    }
    echo "
        </td>
        ";
    echo "
        <td>";
    if ($row["Standort"] == 1) {
        echo "Ja";
    } else {
        echo "Nein";
    }
    echo "
        </td>
        ";
    echo "
        <td>";
    if ($row["Verwendung"] == 1) {
        echo "Ja";
    } else {
        echo "Nein";
    }
    echo "
        </td>
        ";
    echo "
        <td class='cols-md-2'><textarea id='comment" . $row["id"] . "' rows='1' style='width: 100%;'>" . $row["Kurzbeschreibung"] . "</textarea>
        </td>
        ";
    echo "
    </tr>
    ";
}
echo "
    </tbody>
</table>
";
?>
<script>

    $("input[value='Element auswählen']").click(function () {
        let id = this.id;
        $.ajax({
            url: "getElementParameters.php",
            data: {"id": id},
            type: "GET",
            success: function (data) {
                $("#elementParameters").html(data);
            }
        });
    });

    $(document).ready(function () {
        let table = $("#tableRoomElements").DataTable({
            searching: true,
            info: true,
            responsive: true,
            select: true,
            order: [[1, "asc"]],
            lengthChange: false,
            language: {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
            columnDefs: [
                {"targets": [0], "visible": false, "searchable": false}
            ],
            paging: false,
            sDom: "tli"//
        });

        $('#tableRoomElements tbody').on('click', 'tr', function () {
            if ($(this).hasClass('info')) {
            } else {
                table.$('tr.info').removeClass('info');
                $(this).addClass('info');
                let raumbuchID = table.row($(this)).data()[0];
                $.ajax({
                    url: "getElementParameters.php",
                    data: {"id": raumbuchID},
                    type: "GET",
                    success: function (data) {
                        $("#elementParameters").html(data);
                    }
                });

            }
        });
    });

</script>
</body>
</html>