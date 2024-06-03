<?php
session_start();
include '_utils.php';
check_login();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    </head>
    <body>
        <?php
        $mysqli = utils_connect_sql();
        $sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_elemente.ElementID, tabelle_elemente.Kurzbeschreibung As `Elementbeschreibung`, tabelle_varianten.Variante, tabelle_elemente.Bezeichnung, tabelle_geraete.GeraeteID, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete
			FROM tabelle_varianten INNER JOIN (tabelle_hersteller RIGHT JOIN ((tabelle_räume_has_tabelle_elemente LEFT JOIN tabelle_geraete ON tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete = tabelle_geraete.idTABELLE_Geraete) INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
			WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" . $_SESSION["roomID"] . "))
			ORDER BY tabelle_elemente.ElementID;";
        $tabelle_räume_has_tabelle_elemente = $mysqli->query($sql);
        $mysqli->close();

        // -------------------------Elemente im Raum laden-------------------------- 
        $sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl,
            tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            FROM tabelle_varianten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_elemente ON 
            tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON
            tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            WHERE (((tabelle_räume_has_tabelle_elemente.Verwendung)=1))
            GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
            tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
            HAVING (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" .  $_SESSION["roomID"]. ") AND SummevonAnzahl > 0)
            ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante;";
        $tabelle_elemente = $mysqli->query($sql);
        // -----------------Projekt Elementparameter/Variantenparameter laden----------------------------
        $sql = "SELECT tabelle_parameter_kategorie.Kategorie,tabelle_parameter.Abkuerzung, tabelle_parameter.Bezeichnung, tabelle_parameter.idTABELLE_Parameter, tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, tabelle_parameter.Abkuerzung
            FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) 
            ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
            WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND tabelle_parameter.`Bauangaben relevant` = 1)
            GROUP BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung
            ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";
        $tabelle_parameter_kategorie = $mysqli->query($sql); // $paramInfos
// -------------------------Elemente parameter ------------------------- 
        $sql = "SELECT tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, 
            tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie, tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, tabelle_parameter.idTABELLE_Parameter 
            FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) 
            ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
            WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND tabelle_parameter.`Bauangaben relevant` = 1)
            ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";
        $tabelle_projekt_elementparameter = $mysqli->query($sql);   // $elementParamInfos

//        foreach ($tabelle_parameter_kategorie as $ParameterInfo) {
//            $tmp_txt = $ParameterInfo['Bezeichnung'];
//            $tmp_parameterID = $ParameterInfo['ParamID'];
//            foreach ($tabelle_projekt_elementparameter as $elementParamInfo) {
//                if ($elementParamInfo['ParamID'] == $tmp_parameterID && $elementParamInfo['elementID'] == $row['TABELLE_Elemente_idTABELLE_Elemente'] &&
//                        $elementParamInfo['variantenID'] == $row['tabelle_Varianten_idtabelle_Varianten']) {
////                    $outputValue = $elementParamInfo['Wert'] . "" . $elementParamInfo['Einheit'];
//                }
//            }
//        }

        echo" <table class='table table-striped table-bordered table-sm' id='roomElementsParamTable' cellspacing='0' width='100%'>
	<thead <tr></tr> </thead> <tbody> <td></td>  </tbody>";
        ?>
        <script>
            var table;
            $(document).ready(function () {
            var table = $("#roomElementsParamTable").DataTable({
            searching: true,
                    info: true,
                    select: true,
                    order: [[1, "asc"]],
                    lengthChange: false,
                    language: {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                    columnDefs: [
                    {"targets": [0], "visible": false, "searchable": false}
                    ],
                    paging: true,
                    pagingType: "numbers", 
                    pageLength: "25",
                    sDom: "tlip"
            });
//                $('#tableRoomElements tbody').on('click', 'tr', function () {
//                    if ($(this).hasClass('info')) {
//                    } else {
//                        table.$('tr.info').removeClass('info');
//                        $(this).addClass('info');
//                        var raumbuchID = table.row($(this)).data()[0];
//                        $.ajax({
//                            url: "getElementParameters.php",
//                            data: {"id": raumbuchID},
//                            type: "GET",
//                            success: function (data) {
//                                $("#elementParameters").html(data);
//                            }
            });
//
//                    }
//                });
            });


        </script>

    </body>
</html>