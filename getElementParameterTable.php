<?php

include "ElementParameterDefinitions.php";

function generateSelectField($type, $options, $id, $currentValue)
{
    $idAttr = htmlspecialchars("{$type}_{$id}");
    $html = "<td><select class='form-select form-select-sm' id='{$idAttr}'>";
    foreach ($options as $option) {
        $optionEsc = htmlspecialchars($option);
        $selected = ($currentValue === $option) ? " selected" : "";
        $html .= "<option value='{$optionEsc}'{$selected}>{$optionEsc}</option>";
    }
    $html .= "</select></td>";
    return $html;
}

function generate_parameter_input($row, $type)
{
    global $parameterFieldConfig; // Or pass as parameter / use static

    $id = $row["tabelle_parameter_idTABELLE_Parameter"];
    $currentValue = $row[$type];

    $key = "{$row['Kategorie']}|{$row['Bezeichnung']}|{$type}";
    if (isset($parameterFieldConfig[$key])) {
        return generateSelectField($type, $parameterFieldConfig[$key], $id, $currentValue);
    }

    // Default: text input
    $idAttr = htmlspecialchars("{$type}_{$id}");
    $valueAttr = htmlspecialchars($currentValue);
    return "<td><input type='text' class='form-control form-control-sm' id='{$idAttr}' value='{$valueAttr}' size='30'></td>";
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
        echo "<td><button type='button' id='" . $row["tabelle_parameter_idTABELLE_Parameter"] . "' class='btn btn-warning btn-sm' value='saveParameter'><i class='far fa-save'></i></button></td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
    $mysqli->close();

}