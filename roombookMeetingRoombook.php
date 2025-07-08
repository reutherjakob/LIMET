<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
init_page_serversides();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<html lang="de">
<head>
    <title> Rauminhalt </title>
</head>

<body>
<?php
function createTableRow($result): void
{
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["ElementID"] . " " . $row["Bezeichnung"] . "</td>";
        echo "<td>" . $row["Anzahl"] . "</td>";
        echo "<td>";
        if ($row["Neu/Bestand"] == "0") {
            echo "Ja";
        } else {
            echo "Nein";
        }
        echo "</td>";
        echo "<td>" . $row["Kurzbeschreibung"] . "</td>";
        echo "</tr>";
    }
}

$mysqli = utils_connect_sql();


// ORTSFESTE MT--------------------------------------------------------
$sql = "SELECT tabelle_räume_has_tabelle_elemente.id,
       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
       tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten,
       tabelle_räume_has_tabelle_elemente.Anzahl,
       tabelle_elemente.ElementID,
       tabelle_elemente.Kurzbeschreibung AS Elementbeschreibung,
       tabelle_varianten.Variante,
       tabelle_elemente.Bezeichnung,
       tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
       tabelle_räume_has_tabelle_elemente.Standort,
       tabelle_räume_has_tabelle_elemente.Verwendung,
       tabelle_räume_has_tabelle_elemente.Kurzbeschreibung
    FROM tabelle_element_gewerke
         INNER JOIN (tabelle_element_hauptgruppe INNER JOIN (tabelle_element_gruppe INNER JOIN (tabelle_varianten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_elemente
                                                                                                                              ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente =
                                                                                                                                 tabelle_elemente.idTABELLE_Elemente)
                                                                                                ON tabelle_varianten.idtabelle_Varianten =
                                                                                                   tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)
                                                             ON tabelle_element_gruppe.idTABELLE_Element_Gruppe =
                                                                tabelle_elemente.tabelle_element_gruppe_idTABELLE_Element_Gruppe)
                     ON tabelle_element_hauptgruppe.idTABELLE_Element_Hauptgruppe =
                        tabelle_element_gruppe.tabelle_element_hauptgruppe_idTABELLE_Element_Hauptgruppe)
                    ON tabelle_element_gewerke.idtabelle_element_gewerke =
                       tabelle_element_hauptgruppe.tabelle_element_gewerke_idtabelle_element_gewerke
    WHERE tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = " . $_SESSION["roomID"] . " AND
       tabelle_räume_has_tabelle_elemente.Anzahl <> 0 AND
       tabelle_element_gewerke.Nummer= 1
    ORDER BY tabelle_elemente.ElementID;";
$result = $mysqli->query($sql);

echo "
        <div class='row mt-4'>
            
            <div class='col-xxl-6'>   
                <div class='card  m-2'>
                    <div class='card-header'>
                        <h4 class='m-b-2 text-dark'><i class='fas fa-weight-hanging'></i> Ortsfeste MT</h4>
                    </div>
                    <div class='card-body'>";
echo "<table class='table overflow-hidden table-responsive table-striped table-bordered table-sm   table-hover border border-light border-5' id='ofMT' >
                                <thead><tr> 
                                <th>Element</th>
                                <th>Stk</th>
                                <th>Bestand</th>
                                <th>Kom.</th>    
                                </tr></thead>
                                <tbody>";
createTableRow($result);
echo "</tbody></table> </div></div></div>";
//---------------------------------------------------------------------
// OV MT TABELLE--------------------------------------------------------
$sql = "SELECT tabelle_räume_has_tabelle_elemente.id,
       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
       tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten,
       tabelle_räume_has_tabelle_elemente.Anzahl,
       tabelle_elemente.ElementID,
       tabelle_elemente.Kurzbeschreibung AS Elementbeschreibung,
       tabelle_varianten.Variante,
       tabelle_elemente.Bezeichnung,
       tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
       tabelle_räume_has_tabelle_elemente.Standort,
       tabelle_räume_has_tabelle_elemente.Verwendung,
       tabelle_räume_has_tabelle_elemente.Kurzbeschreibung
    FROM tabelle_element_gewerke
         INNER JOIN (tabelle_element_hauptgruppe INNER JOIN (tabelle_element_gruppe INNER JOIN (tabelle_varianten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_elemente
                                                                                                                              ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente =
                                                                                                                                 tabelle_elemente.idTABELLE_Elemente)
                                                                                                ON tabelle_varianten.idtabelle_Varianten =
                                                                                                   tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)
                                                             ON tabelle_element_gruppe.idTABELLE_Element_Gruppe =
                                                                tabelle_elemente.tabelle_element_gruppe_idTABELLE_Element_Gruppe)
                     ON tabelle_element_hauptgruppe.idTABELLE_Element_Hauptgruppe =
                        tabelle_element_gruppe.tabelle_element_hauptgruppe_idTABELLE_Element_Hauptgruppe)
                    ON tabelle_element_gewerke.idtabelle_element_gewerke =
                       tabelle_element_hauptgruppe.tabelle_element_gewerke_idtabelle_element_gewerke
    WHERE   tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = " . $_SESSION["roomID"] . " AND
            tabelle_räume_has_tabelle_elemente.Anzahl <> 0 AND
            tabelle_element_gewerke.Nummer = 2
    ORDER BY tabelle_elemente.ElementID;";
$result = $mysqli->query($sql);

echo "<div class='col-xxl-6'> 
                <div class='card  m-2'>
                    <div class='card-header'>
                        <h4 class='text-dark'><i class='fas fa-stethoscope'></i> Ortsveränderliche MT</h4>
                    </div>
                    <div class='card-body'>";
echo "<table class='table overflow-hidden table-responsive table-striped table-bordered table-sm   table-hover border border-light border-5' id='ovMT' >
                                <thead><tr> 
                                <th>Element</th>
                                <th>Stk</th>
                                <th>Bestand</th>
                                <th>Kom.</th>    
                                </tr></thead>
                                <tbody>";
createTableRow($result);
echo "</tbody></table></div></div></div></div>";
//---------------------------------------------------------------------
// Möbel MT TABELLE--------------------------------------------------------
$sql = "SELECT tabelle_räume_has_tabelle_elemente.id,
       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
       tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten,
       tabelle_räume_has_tabelle_elemente.Anzahl,
       tabelle_elemente.ElementID,
       tabelle_elemente.Kurzbeschreibung AS Elementbeschreibung,
       tabelle_varianten.Variante,
       tabelle_elemente.Bezeichnung,
       tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
       tabelle_räume_has_tabelle_elemente.Standort,
       tabelle_räume_has_tabelle_elemente.Verwendung,
       tabelle_räume_has_tabelle_elemente.Kurzbeschreibung
    FROM tabelle_element_gewerke
         INNER JOIN (tabelle_element_hauptgruppe INNER JOIN (tabelle_element_gruppe INNER JOIN (tabelle_varianten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_elemente
                                                                                                                              ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente =
                                                                                                                                 tabelle_elemente.idTABELLE_Elemente)
                                                                                                ON tabelle_varianten.idtabelle_Varianten =
                                                                                                   tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)
                                                             ON tabelle_element_gruppe.idTABELLE_Element_Gruppe =
                                                                tabelle_elemente.tabelle_element_gruppe_idTABELLE_Element_Gruppe)
                     ON tabelle_element_hauptgruppe.idTABELLE_Element_Hauptgruppe =
                        tabelle_element_gruppe.tabelle_element_hauptgruppe_idTABELLE_Element_Hauptgruppe)
                    ON tabelle_element_gewerke.idtabelle_element_gewerke =
                       tabelle_element_hauptgruppe.tabelle_element_gewerke_idtabelle_element_gewerke
    WHERE tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = " . $_SESSION["roomID"] . " AND
       tabelle_räume_has_tabelle_elemente.Anzahl <> 0 AND
       tabelle_element_gewerke.Nummer = 4
    ORDER BY tabelle_elemente.ElementID;";
$result = $mysqli->query($sql);

echo "<div class='row mt-4'>
            <div class='col-xxl-6'> 
                <div class='card  m-2'>
                    <div class='card-header'>
                        <h4 class='text-dark'><i class='fas fa-arrows-alt'></i> Möbel</h4>
                    </div>
                    <div class='card-body'>";
echo "<table class='table overflow-hidden table-responsive table-striped table-bordered table-sm   table-hover border border-light border-5' id='moebel' >
                                <thead><tr> 
                                <th>Element</th>
                                <th>Stk</th>
                                <th>Bestand</th>
                                <th>Kom.</th>    
                                </tr></thead>
                                <tbody>";
createTableRow($result);
echo "</tbody></table></div></div></div>";
//---------------------------------------------------------------------
// Medizinische Gase TABELLE--------------------------------------------------------
$sql = "SELECT tabelle_räume_has_tabelle_elemente.id,
       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
       tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten,
       tabelle_räume_has_tabelle_elemente.Anzahl,
       tabelle_elemente.ElementID,
       tabelle_elemente.Kurzbeschreibung AS Elementbeschreibung,
       tabelle_varianten.Variante,
       tabelle_elemente.Bezeichnung,
       tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
       tabelle_räume_has_tabelle_elemente.Standort,
       tabelle_räume_has_tabelle_elemente.Verwendung,
       tabelle_räume_has_tabelle_elemente.Kurzbeschreibung
    FROM tabelle_element_gewerke
         INNER JOIN (tabelle_element_hauptgruppe INNER JOIN (tabelle_element_gruppe INNER JOIN (tabelle_varianten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_elemente
                                                                                                                              ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente =
                                                                                                                                 tabelle_elemente.idTABELLE_Elemente)
                                                                                                ON tabelle_varianten.idtabelle_Varianten =
                                                                                                   tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)
                                                             ON tabelle_element_gruppe.idTABELLE_Element_Gruppe =
                                                                tabelle_elemente.tabelle_element_gruppe_idTABELLE_Element_Gruppe)
                     ON tabelle_element_hauptgruppe.idTABELLE_Element_Hauptgruppe =
                        tabelle_element_gruppe.tabelle_element_hauptgruppe_idTABELLE_Element_Hauptgruppe)
                    ON tabelle_element_gewerke.idtabelle_element_gewerke =
                       tabelle_element_hauptgruppe.tabelle_element_gewerke_idtabelle_element_gewerke
    WHERE tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = " . $_SESSION["roomID"] . " AND
       tabelle_räume_has_tabelle_elemente.Anzahl <> 0 AND
       tabelle_element_gewerke.Nummer = 5
    ORDER BY tabelle_elemente.ElementID;";
$result = $mysqli->query($sql);

echo "
            <div class='col-xxl-6'> 
                <div class='card  m-2'>
                    <div class='card-header'>
                        <h4 class='text-dark'><i class='fas fa-arrows-alt'></i> Medizinische Gase</h4>
                    </div>
                    <div class='card-body'>";
echo "<table class='table overflow-hidden table-responsive table-striped table-bordered table-sm   table-hover border border-light border-5' id='gase' >
                                <thead><tr> 
                                <th>Element</th>
                                <th>Stk</th>
                                <th>Bestand</th> 
                                <th>Kom.</th>    
                                </tr></thead>
                                <tbody>";

createTableRow($result);
echo "</tbody></table>
</div></div></div></div>";
//---------------------------------------------------------------------
// LaborausstattungTABELLE--------------------------------------------------------
$sql = "SELECT tabelle_räume_has_tabelle_elemente.id,
       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
       tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten,
       tabelle_räume_has_tabelle_elemente.Anzahl,
       tabelle_elemente.ElementID,
       tabelle_elemente.Kurzbeschreibung AS Elementbeschreibung,
       tabelle_varianten.Variante,
       tabelle_elemente.Bezeichnung,
       tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
       tabelle_räume_has_tabelle_elemente.Standort,
       tabelle_räume_has_tabelle_elemente.Verwendung,
       tabelle_räume_has_tabelle_elemente.Kurzbeschreibung
    FROM tabelle_element_gewerke
         INNER JOIN (tabelle_element_hauptgruppe INNER JOIN (tabelle_element_gruppe INNER JOIN (tabelle_varianten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_elemente
                                                                                                                              ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente =
                                                                                                                                 tabelle_elemente.idTABELLE_Elemente)
                                                                                                ON tabelle_varianten.idtabelle_Varianten =
                                                                                                   tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)
                                                             ON tabelle_element_gruppe.idTABELLE_Element_Gruppe =
                                                                tabelle_elemente.tabelle_element_gruppe_idTABELLE_Element_Gruppe)
                     ON tabelle_element_hauptgruppe.idTABELLE_Element_Hauptgruppe =
                        tabelle_element_gruppe.tabelle_element_hauptgruppe_idTABELLE_Element_Hauptgruppe)
                    ON tabelle_element_gewerke.idtabelle_element_gewerke =
                       tabelle_element_hauptgruppe.tabelle_element_gewerke_idtabelle_element_gewerke
    WHERE   tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = " . $_SESSION["roomID"] . " AND
            tabelle_räume_has_tabelle_elemente.Anzahl <> 0 AND
            tabelle_element_gewerke.Nummer = 9
    ORDER BY tabelle_elemente.ElementID;";
$result = $mysqli->query($sql);

echo "
        <div class='row mt-4'>
            <div class='col-xxl-6'> 
                <div class='card  m-2'>
                    <div class='card-header'>
                        <h4 class='text-dark'><i class='fas fa-flask'></i> Laborausstattung</h4>
                    </div>
                    <div class='card-body'>";
echo "<table class='table overflow-hidden table-responsive table-striped table-bordered table-sm   table-hover border border-light border-5' id='labor' >
                                <thead><tr> 
                                <th>Element</th>
                                <th>Stk</th>
                                <th>Bestand</th>
                                <th>Kom.</th>    
                                </tr></thead>
                                <tbody>";
createTableRow($result);
echo "</tbody></table></div></div></div></div> ";
//---------------------------------------------------------------------


$mysqli->close();
?>
<script>
    $(document).ready(function () {
        $("#ofMT").DataTable({
            "select": false,
            "paging": false,
            "searching": false,
            "info": false,
            "order": [[0, "asc"]],
            "language": {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"}
        });
        $("#ovMT").DataTable({
            "select": false,
            "paging": false,
            "searching": false,
            "info": false,
            "order": [[0, "asc"]],
            "language": {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"}
        });
        $("#moebel").DataTable({
            "select": false,
            "paging": false,
            "searching": false,
            "info": false,
            "order": [[0, "asc"]],
            "language": {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"}
        });
        $("#gase").DataTable({
            "select": false,
            "paging": false,
            "searching": false,
            "info": false,
            "order": [[0, "asc"]],
            "language": {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"}
        });
        $("#labor").DataTable({
            "select": false,
            "paging": false,
            "searching": false,
            "info": false,
            "order": [[0, "asc"]],
            "language": {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"}
        });
    });
</script>

</body>
</html>