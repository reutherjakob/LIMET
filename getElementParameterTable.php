<?php
function generateEinheitField($kategorie, $bezeichnung, $type, $options, $id, $currentValue, $row): string
{
    if (
        $row["Kategorie"] === $kategorie &&
        $row["Bezeichnung"] === $bezeichnung &&
        $type === "Einheit"
    ) {
        $html = "<td><select class='form-select form-select-sm' id='{$type}_{$id}'>";
        foreach ($options as $option) {
            $selected = $currentValue === $option ? "selected" : "";
            $html .= "<option value='{$option}' {$selected}>{$option}</option>";
        }
        $html .= "</select></td>";
        return $html;
    }
    return "";
}
function generateWertField($kategorie, $bezeichnung, $type, $options, $id, $currentValue, $row): string
{
    if (
        $row["Kategorie"] === $kategorie &&
        $row["Bezeichnung"] === $bezeichnung &&
        $type === "Wert"
    ) {
        $html = "<td><select class='form-select form-select-sm' id='{$type}_{$id}'>";
        foreach ($options as $option) {
            $selected = $currentValue === $option ? "selected" : "";
            $html .= "<option value='{$option}' {$selected}>{$option}</option>";
        }
        $html .= "</select></td>";
        return $html;
    }
    return "";
}


function generate_parameter_input($row, $type): string
{
    $id = $row["tabelle_parameter_idTABELLE_Parameter"];
    $currentValue = $row[$type];

    $html = generateWertField("Statik", "Wandverstärkung", $type, [  "50", "100", "150", "200", "Ja"], $id, $currentValue, $row);
    if ($html) return $html;

    $html = generateEinheitField("Statik", "Wandverstärkung", $type, ["kg/lfm", ""], $id, $currentValue, $row);
    if ($html) return $html;

    $html = generateEinheitField("Elektro", "Spannung", $type, ["V", "kV", "V DC"], $id, $currentValue, $row);
    if ($html) return $html;

    $html = generateEinheitField("Elektro", "Nennleistung", $type, ["W", "kW", "VA", "kVA"], $id, $currentValue, $row);
    if ($html) return $html;

    $html = generateEinheitField("HKLS", "Abwärme", $type, ["W", "kW"], $id, $currentValue, $row);
    if ($html) return $html;

    // Netzart special case
    if ($row["Kategorie"] === "Elektro" && $row["Bezeichnung"] === "Netzart") {
        $options = ($type === "Wert")
            ? ["", "AV", "SV", "ZSV", "USV", "AV/SV", "SV/ZSV", "ZSV/USV", "Akku"]
            : ["", "/Akku"];

        $html = "<td><select class='form-select form-select-sm' id='{$type}_{$id}'>";
        foreach ($options as $option) {
            $selected = $currentValue === $option ? "selected" : "";
            $html .= "<option value='{$option}' {$selected}>{$option}</option>";
        }
        $html .= "</select></td>";
        return $html;
    }

    return "<td><input type='text' class='form-control form-control-sm' id='{$type}_{$id}' value='" . htmlspecialchars($currentValue) . "' size='30'></td>";
}


function generate_variante_parameter_inputtable(): void
{
    $mysqli = utils_connect_sql();
    $sql = "SELECT tabelle_parameter.Bezeichnung, tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_parameter_kategorie.Kategorie, tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter
            FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
            WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente)=" . $_SESSION["elementID"] . ") AND ((tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten)=" . $_SESSION["variantenID"] . "));";
    $result = $mysqli->query($sql);

    echo "
    <table class='table table-striped table-sm table-hover table-bordered border border-light border-5' id='tableElementParameters'>
	<thead><tr>
	<th></th>
	<th>Kategorie</th>
	<th>Parameter</th>
	<th>Wert</th>
	<th>Einheit</th>
	<th> </th>
	</tr></thead>
	<tbody>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><button type='button' id='" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' class='btn btn-outline-danger btn-sm' value='deleteParameter'><i class='fas fa-minus'></i></button></td>";
        echo "<td>" . $row["Kategorie"] . "</td>";
        echo "<td>" . $row["Bezeichnung"] . "</td>";
        echo generate_parameter_input($row, 'Wert');
        echo generate_parameter_input($row, 'Einheit');
        // echo "<td>< type='text' id='wert" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' value='" . $row["Wert"] . "' size='30'></td>";
        // echo "<td>< type='text' id='einheit" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' value='" . $row["Einheit"] . "' size='30'></td>";
        echo "<td><button type='button' id='" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' class='btn btn-warning btn-sm' value='saveParameter'><i class='far fa-save'></i></button></td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
    $mysqli->close();

}