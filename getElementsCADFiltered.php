<?php
require_once 'utils/_utils.php';
check_login();

$filterDWGNotwendig = getPostInt('filterValueDWGNotwendig');
$filterDWGVorhanden = getPostInt('filterValueDWGVorhanden');
$filterFamilieVorhanden = getPostInt('filterValueFamilieVorhanden');

$mysqli = utils_connect_sql();

$baseSql = "SELECT `idTABELLE_Elemente`, `Bezeichnung`, `ElementID`, `Kurzbeschreibung`, 
            `CAD_notwendig`, `CAD_dwg_vorhanden`, `CAD_dwg_kontrolliert`, 
            `CAD_familie_vorhanden`, `CAD_familie_kontrolliert`, `CAD_Kommentar`
            FROM `LIMET_RB`.`tabelle_elemente`";

$whereClauses = [];
$params = [];
$paramTypes = "";

if ($filterDWGNotwendig !== 2) {
    $whereClauses[] = "`CAD_notwendig` = ?";
    $params[] = $filterDWGNotwendig;
    $paramTypes .= "i";
}
if ($filterDWGVorhanden !== 2) {
    $whereClauses[] = "`CAD_dwg_vorhanden` = ?";
    $params[] = $filterDWGVorhanden;
    $paramTypes .= "i";
}
if ($filterFamilieVorhanden !== 2) {
    $whereClauses[] = "`CAD_familie_vorhanden` = ?";
    $params[] = $filterFamilieVorhanden;
    $paramTypes .= "i";
}

if (count($whereClauses) > 0) {
    $baseSql .= " WHERE " . implode(" AND ", $whereClauses);
}
$baseSql .= " ORDER BY `ElementID`";

$stmt = $mysqli->prepare($baseSql);

if ($params) {
    $stmt->bind_param($paramTypes, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

echo "<table id='tableElements' class='table table-striped table-bordered table-condensed'>
    <thead><tr>
    <th>ID</th>
    <th>Element</th>
    <th>Beschreibung</th>
    <th>CAD Notwendigkeit
        <select class='form-control input-sm' id='filter_dwg_notwendig'>
            <option value='2'" . ($filterDWGNotwendig === 2 ? " selected" : "") . "></option>
            <option value='0'" . ($filterDWGNotwendig === 0 ? " selected" : "") . ">Nein</option>
            <option value='1'" . ($filterDWGNotwendig === 1 ? " selected" : "") . ">Ja</option>
        </select>
    </th>
    <th>DWG vorhanden
        <select class='form-control input-sm' id='filterCAD_dwg_vorhanden'>
            <option value='2'" . ($filterDWGVorhanden === 2 ? " selected" : "") . "></option>
            <option value='0'" . ($filterDWGVorhanden === 0 ? " selected" : "") . ">Nein</option>
            <option value='1'" . ($filterDWGVorhanden === 1 ? " selected" : "") . ">Ja</option>
        </select>
    </th>
    <th>DWG geprüft</th>
    <th>Familie vorhanden
        <select class='form-control input-sm' id='filterCAD_familie_vorhanden'>
            <option value='2'" . ($filterFamilieVorhanden === 2 ? " selected" : "") . "></option>
            <option value='0'" . ($filterFamilieVorhanden === 0 ? " selected" : "") . ">Nein</option>
            <option value='1'" . ($filterFamilieVorhanden === 1 ? " selected" : "") . ">Ja</option>
        </select>
    </th>
    <th>Familie geprüft</th>
    <th>CAD Kommentar</th>
    <th>Speichern</th>
    </tr></thead>
    <tfoot><tr>
    <th>ID</th>
    <th>Element</th>
    <th>Beschreibung</th>
    <th>CAD Notwendigkeit</th>
    <th>DWG vorhanden</th>
    <th>DWG geprüft</th>
    <th>Familie vorhanden</th>
    <th>Familie geprüft</th>
    <th>CAD Kommentar</th>
    <th>Speichern</th>
    </tr></tfoot><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row["ElementID"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["Bezeichnung"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["Kurzbeschreibung"]) . "</td>";

    $selectOptionsYesNo = function ($selected) {
        return "<option" . ($selected === 0 ? " selected" : "") . ">Nein</option>
                <option" . ($selected === 1 ? " selected" : "") . ">Ja</option>";
    };
    $selectOptionsKontrolliert = function ($selected) {
        $options = [
            0 => "Nicht geprüft",
            1 => "Freigegeben",
            2 => "Überarbeiten"
        ];
        $output = "";
        foreach ($options as $val => $label) {
            $output .= "<option" . ($selected === $val ? " selected" : "") . ">$label</option>";
        }
        return $output;
    };

    echo "<td><select class='form-control input-sm' id='selectCAD_notwendig" . $row["idTABELLE_Elemente"] . "'>" . $selectOptionsYesNo($row["CAD_notwendig"]) . "</select></td>";
    echo "<td><select class='form-control input-sm' id='selectCAD_dwg_vorhanden" . $row["idTABELLE_Elemente"] . "'>" . $selectOptionsYesNo($row["CAD_dwg_vorhanden"]) . "</select></td>";
    echo "<td><select class='form-control input-sm' id='selectCAD_dwg_kontrolliert" . $row["idTABELLE_Elemente"] . "'>" . $selectOptionsKontrolliert($row["CAD_dwg_kontrolliert"]) . "</select></td>";
    echo "<td><select class='form-control input-sm' id='selectCAD_familie_vorhanden" . $row["idTABELLE_Elemente"] . "'>" . $selectOptionsYesNo($row["CAD_familie_vorhanden"]) . "</select></td>";
    echo "<td><select class='form-control input-sm' id='selectCAD_familie_kontrolliert" . $row["idTABELLE_Elemente"] . "'>" . $selectOptionsKontrolliert($row["CAD_familie_kontrolliert"]) . "</select></td>";
    echo "<td><textarea id='CADcomment" . $row["idTABELLE_Elemente"] . "' class='form-control' style='width: 100%; height: 100%;'>" . htmlspecialchars($row["CAD_Kommentar"]) . "</textarea></td>";
    echo "<td><input type='button' id='" . $row["idTABELLE_Elemente"] . "' class='btn btn-warning btn-sm' value='Speichern'></td>";
    echo "</tr>";
}

echo "</tbody></table>";
$stmt->close();
$mysqli->close();
?>

<script>
    $(document).ready(function () {
        $('#tableElements').DataTable({
            paging: true,
            ordering: false,
            pagingType: "simple_numbers",
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            language: {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"}
        });
    });

    ['dwg_vorhanden', 'dwg_notwendig', 'familie_vorhanden'].forEach(function(filterId) {
        $('#filterCAD_' + filterId + ', #filter_' + filterId).change(function () {
            let filterValueDWGVorhanden = $('#filterCAD_dwg_vorhanden').val();
            let filterValueDWGNotwendig = $('#filter_dwg_notwendig').val();
            let filterValueFamilieVorhanden = $('#filterCAD_familie_vorhanden').val();
            $.ajax({
                url: "getElementsCADFiltered.php",
                data: {
                    "filterValueDWGNotwendig": filterValueDWGNotwendig,
                    "filterValueDWGVorhanden": filterValueDWGVorhanden,
                    "filterValueFamilieVorhanden": filterValueFamilieVorhanden
                },
                type: "POST",
                success: function (data) {
                    $("#cadElements").html(data);
                }
            });
        });
    });

</script>
