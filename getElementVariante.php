<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<html lang="de">
<head>
    <title>Get Element Variante</title></head>
<body>

<?php
if (!function_exists('utils_connect_sql')) {
    include "utils/_utils.php";
}
if (filter_input(INPUT_GET, 'elementID') != "") {
    $_SESSION["elementID"] = filter_input(INPUT_GET, 'elementID');
}
if (filter_input(INPUT_GET, 'variantenID') != "") {
    $_SESSION["variantenID"] = filter_input(INPUT_GET, 'variantenID');
}
$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_projekt_varianten_kosten.Kosten
			FROM tabelle_projekt_varianten_kosten
			WHERE (((tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)=" . $_SESSION["variantenID"] . ") AND ((tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)=" . $_SESSION["elementID"] . ") AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "));";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc(); ?>
<div class='col-xxl-12'>
    <div class='card'>
        <div class='card-body d-inline-flex'>
            <div class=' d-flex align-items-center flex-wrap'>
                <div class='form-group d-flex align-items-center mr-2'>
                    <label for='variante'>Variante </label>
                    <select class='form-control form-control-sm me-1 ms-1' id='variante'>
                        <?php
                        $options = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
                        $selectedID = $_SESSION["variantenID"];
                        foreach ($options as $key => $value) {
                            $optionValue = $key + 1;
                            $selected = ($optionValue == $selectedID) ? 'selected' : '';
                            echo "<option value='$optionValue' $selected>$value</option>";
                        }
                        ?>
                    </select>
                </div>
                &nbsp;
                <div class='form-group d-flex align-items-center mr-2'>
                    <label for='kosten'>Kosten </label>
                    <input type='text' class='form-control form-control-sm' id='kosten'
                           value="<?php echo $row['Kosten']; ?>">
                </div>
                &nbsp;
                <div class='form-group d-flex align-items-center mr-2'>
                    <label>&nbsp;</label>
                    <div>
                        <button type='button' id='saveVariantePrice'
                                class='btn btn-outline-dark btn-sm'>
                            <i class='far fa-save'></i> Kosten speichern
                        </button>
                        <button type='button' id='getElementPriceHistory'
                                class='btn btn-outline-dark btn-sm'
                                data-bs-toggle='modal' data-bs-target='#getElementPriceHistoryModal'>
                            <i class='far fa-clock'></i> Kosten Änderungsverlauf
                        </button>

                        <button type='button' id='addVariantenParameters'
                                class='btn btn-outline-dark btn-sm m-1' value='addVariantenParameters'
                                data-bs-toggle='modal'
                                data-bs-target='#addVariantenParameterToElementModal'><i
                                    class='fas fa-upload'></i> Variantenparameter übernehmen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class='row'>
        <div class="col-xxl-6">
            <div class='card'>
                <div class='card-header'>
                    <div class='row'>
                        <div class='col-xxl-10'>
                            Variantenparameter
                        </div>
                        <div class="col-xxl-2 d-flex align-items-center justify-content-end"
                             id="variantenParameterCH">
                            <button type='button' class='btn btn-warning btn-sm text-nowrap' value='saveAllParameter'><i
                                        class='far fa-save'> Alle </i></button>
                        </div>
                    </div>
                </div>
                <div class='card-body ' id='variantenParameter'>
                    <?php
                    include "getElementParameterTable.php";
                    generate_variante_parameter_inputtable();
                    ?>
                </div>
            </div>
        </div>


        <div class='col-xxl-6'>
            <div class='card'>
                <div class='card-header d-flex justify-content-between' id='mglParameterCardHeader'>
                    Mögliche Parameter
                </div>
                <div class='card-body ' id='possibleVariantenParameter'>
                    <?php $sql = "SELECT tabelle_parameter.idTABELLE_Parameter, tabelle_parameter.Bezeichnung, 
                                tabelle_parameter.Abkuerzung, 
                                tabelle_parameter_kategorie.Kategorie
                                FROM tabelle_parameter, tabelle_parameter_kategorie
                                WHERE tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie =
                                tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
                                AND tabelle_parameter.idTABELLE_Parameter NOT IN
                                (SELECT tabelle_parameter.idTABELLE_Parameter
                                FROM tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON
                                tabelle_parameter.idTABELLE_Parameter =
                                tabelle_projekt_elementparameter.TABELLE_Parameter_idTABELLE_Parameter
                                WHERE tabelle_projekt_elementparameter.TABELLE_Elemente_idTABELLE_Elemente = " .
                        $_SESSION["elementID"] . " AND
                                tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte = " .
                        $_SESSION["projectID"] . " AND
                                tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten = " .
                        $_SESSION["variantenID"] . ")
                                ORDER BY 
                                CASE 
                                    WHEN tabelle_parameter.Bezeichnung = 'Nennleistung' 
                                    AND tabelle_parameter_kategorie.Kategorie = 'Elektro' THEN 0 
                                    ELSE 1 
                                END,
                                tabelle_parameter_kategorie.Kategorie,
                                tabelle_parameter.Bezeichnung;";

                    $result = $mysqli->query($sql);
                    echo "<table class='table table-striped table-sm table-hover table-bordered border border-5 border-light' id='tablePossibleElementParameters'>
                                    <thead>
                                    <tr>
                                        <th> <i class='fas fa-plus'></i> </th>
                                        <th>Kategorie</th>
                                        <th>Parameter</th>
                                        <th>Abk</th>
                                    </tr>
                                    </thead>
                                    <tbody>";

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td> <button type='button' id='" . $row["idTABELLE_Parameter"] . "'
                              class='btn btn-outline-success btn-sm' value='addParameter'>
                              <i class='fas fa-plus'></i></button></td>";
                        echo "<td>" . $row["Kategorie"] . "</td> ";
                        echo "<td>" . $row["Bezeichnung"] . "</td>";
                        echo "<td>" . $row["Abkuerzung"] . "</td>";
                        echo "</tr> ";
                    }

                    echo " </tbody> </table> "; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal zum Zeigen der Kostenänderungen -->
<div class='modal fade' id='getElementPriceHistoryModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Kostenänderungen</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;
                </button>
            </div>
            <div class='modal-body' id='mbody'>
                <?php
                $sql = "SELECT tabelle_varianten.Variante, tabelle_projekt_varianten_kosten_aenderung.kosten_alt, tabelle_projekt_varianten_kosten_aenderung.kosten_neu, tabelle_projekt_varianten_kosten_aenderung.timestamp, tabelle_projekt_varianten_kosten_aenderung.user
							FROM tabelle_varianten INNER JOIN tabelle_projekt_varianten_kosten_aenderung ON tabelle_varianten.idtabelle_Varianten = tabelle_projekt_varianten_kosten_aenderung.variante
							WHERE (((tabelle_projekt_varianten_kosten_aenderung.projekt)=" . $_SESSION["projectID"] . ") AND ((tabelle_projekt_varianten_kosten_aenderung.element)=" . $_SESSION["elementID"] . "))
							ORDER BY tabelle_varianten.Variante, tabelle_projekt_varianten_kosten_aenderung.timestamp DESC;";

                $result = $mysqli->query($sql);
                echo "<table class='table table-striped table-sm' id='tableVariantenCostsOverTime'>
						<thead><tr>
						<th>Variante</th>
						<th>Kosten vorher</th>
						<th>Kosten nachher</th>						
						<th>User</th>
						<th>Datum</th>
						</tr></thead>
						<tbody>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["Variante"] . "</td>";
                    echo "<td>" . $row["kosten_alt"] . "</td>";
                    echo "<td>" . $row["kosten_neu"] . "</td>";
                    echo "<td>" . $row["user"] . "</td>";
                    echo "<td>" . $row["timestamp"] . "</td>";
                    echo "</tr>";
                }

                echo "</tbody></table>";
                $mysqli->close();
                ?>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-danger btn-sm'
                        data-bs-dismiss='modal'>Schließen
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ALERT Modal -->
<div class="modal fade" id="alertModal" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal">&times;
                </button>
                <h4 class="modal-title"><span
                            class='glyphicon glyphicon-info-sign'></span> Info</h4>
            </div>
            <div class="modal-body">
                <p id="error"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Variantenparameter übernehmen Modal -->
<div class='modal fade' id='addVariantenParameterToElementModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-md'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title justify-content-center'>Variantenparameter übernehmen? </h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;
                </button>
            </div>
            <div class='modal-body' id='mbody'>
                Wollen Sie die Elementparameter wirklich
                überschreiben? <br> Kann derzeit nur Hr. Reuther.
            </div>
            <div class='modal-footer row'>
                <div class="d-flex justify-content-center align-items-center">
                    <div class="col-1"></div>
                    <button type='button' id='addVariantenParameterToElement'
                            class='btn btn-success btn-sm col-5 me-1 ms-1' value='Ja'
                            data-bs-dismiss='modal'> Ja
                    </button>

                    <button type='button' class='btn btn-danger btn-sm col-5 me-1 ms-1'
                            data-bs-dismiss='modal'>Nein
                    </button>
                </div>

            </div>
        </div>

    </div>
</div>

<script src="utils/_utils.js"></script>
<script src="saveElementParameters.js"></script>
<script>
    var tablePossibleElementParameters;

    document.getElementById('kosten').addEventListener('keydown', function (event) {
        if (event.key === 'Enter') { //avoid annoying reload of the page, when hitting enter and subsequently causing useless form submission
            event.preventDefault();
        }
    });

    $(document).ready(function () {
        $('#tableElementParameters').DataTable({ //same as in getPossibleVarianteParameters.php
            select: true,
            searching: true,
            pagingType: "simple",
            order: [[1, 'asc']],
            columnDefs: [
                {
                    targets: [0],
                    visible: true,
                    searchable: false,
                    sortable: false
                }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: "",
                searchPlaceholder: "Suche..."
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: ['info', 'search'],
                bottomEnd: ['paging', 'pageLength']
            },
            scrollX: true,
            initComplete: function () {

                $('#variantenParameterCH .xxx').remove();
                $('#variantenParameter .dt-search label').remove();
                $('#variantenParameter .dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark xxx").appendTo('#variantenParameterCH');
            }
        });

        tablePossibleElementParameters = $('#tablePossibleElementParameters').DataTable({
            select: true,
            searching: true,
            info: false,
            columnDefs: [
                {
                    targets: [0],
                    visible: true,
                    searchable: false,
                    sortable: false
                }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: "",
                searchPlaceholder: "Suche..."
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: ['info', 'search'],
                bottomEnd: ['paging', 'pageLength']
            },
            scrollX: true,
            initComplete: function () {
                $('#mglParameterCardHeader .xxx').remove();
                $('#possibleVariantenParameter .dt-search label').remove();
                $('#possibleVariantenParameter .dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark xxx").appendTo('#mglParameterCardHeader');
            }
        });

        tablePossibleElementParameters.rows().every(function () {
            let data = this.data();
            if (data[1].trim() === 'Elektro' && data[2].trim() === 'Nennleistung') {
                this.moveTo(0);
            }
        });

        $('#tableVariantenCostsOverTime').DataTable({
            select: true,
            searching: true,
            info: false,
            columnDefs: [
                {
                    targets: [0],
                    visible: true,
                    searchable: true,
                    sortable: true
                }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                decimal: ',',
                thousands: '.'
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: ['info', 'search'],
                bottomEnd: ['paging', 'pageLength']
            },
            scrollX: true
        });
    })

    // Variante auswählen/geändert
    $('#variante').change(function () {
        let variantenID = this.value;
        $.ajax({
            url: "setSessionVariables.php",
            data: {"variantenID": variantenID},
            type: "GET",
            success: function () {  //console.log("JS:", variantenID);
                $.ajax({
                    url: "getSessionVariante.php",
                    type: "GET",
                    success: function () {
                        $.ajax({
                            url: "getVariantePrice.php",
                            data: {"variantenID": variantenID},
                            type: "GET",
                            success: function (data) {
                                if (data.length === 2) {
                                    $("#error").html("Variante noch nicht vorhanden! Zum Anlegen Kosten eingeben und Speichern!");
                                    $('#alertModal').modal("show");
                                    $("#kosten").val("");
                                    $("#possibleVariantenParameter").hide();
                                    $("#variantenParameter").hide();
                                } else {
                                    $("#kosten").val(data);
                                    $("#possibleVariantenParameter").show();
                                    $("#variantenParameter").show();
                                    $.ajax({
                                        url: "getVarianteParameters.php",
                                        data: {"variantenID": variantenID},
                                        type: "GET",
                                        success: function (data) {
                                            $('#variantenParameterCh .xxx').remove();
                                            $("#variantenParameter").html(data);
                                            $.ajax({
                                                url: "getPossibleVarianteParameters.php",
                                                data: {"variantenID": variantenID},
                                                type: "GET",
                                                success: function (data) {
                                                    $("#possibleVariantenParameter").html(data);
                                                    //console.log("0", data);
                                                }
                                            });
                                        }
                                    });
                                }
                            }
                        });
                    }
                });
            }
        });
    });


    $("#saveVariantePrice").click(function () {    // Kosten für Variante speichern
        if ($('#kosten').val() !== '') {
            let KostenFormatiert = $('#kosten').val();
            if (KostenFormatiert.toLowerCase().endsWith('k')) {
                KostenFormatiert = KostenFormatiert.slice(0, -1) + '000';
            } //console.log(KostenFormatiert.toLowerCase());
            KostenFormatiert = KostenFormatiert.replace(/,/g, '.').replace(/[^0-9.]/g, ''); //console.log(KostenFormatiert.toLowerCase());
            let variantenID = $('#variante').val();
            $.ajax({
                url: "saveVariantePrice.php",
                type: "GET",
                data: {"kosten": KostenFormatiert, "variantenID": variantenID},
                success: function (data) {
                    makeToaster(data.trim(), true);
                    $("#possibleVariantenParameter").show();
                    $("#variantenParameter").show();
                    $.ajax({
                        url: "getVarianteParameters.php",
                        data: {"variantenID": variantenID},
                        type: "GET",
                        success: function (data) {
                            $('#variantenParameterCh .xxx').remove();
                            $("#variantenParameter").html(data);
                            $.ajax({
                                url: "getPossibleVarianteParameters.php",
                                data: {"variantenID": variantenID},
                                type: "GET",
                                success: function (data) {
                                    $("#possibleVariantenParameter").html(data);
                                }
                            });
                        }
                    });
                }
            });
        } else {
            alert("Kosten eingeben!");
        }
    });


    $("button[value='addParameter']").click(function () {
        let variantenID = $('#variante').val();
        $('#variantenParameterCh .xxx').remove();
        let id = this.id;
        if (id !== "") {
            $.ajax({
                url: "addParameterToVariante.php",
                data: {"parameterID": id, "variantenID": variantenID},
                type: "GET",
                success: function (data) {
                    makeToaster(data.trim(), true);
                    $.ajax({
                        url: "getVarianteParameters.php",
                        data: {"variantenID": variantenID},
                        type: "GET",
                        success: function (data) {
                            $('#variantenParameterCh .xxx').remove();
                            $("#variantenParameter").html(data);
                            $.ajax({
                                url: "getPossibleVarianteParameters.php",
                                data: {"variantenID": variantenID},
                                type: "GET",
                                success: function (data) {
                                    $("#possibleVariantenParameter").html(data);

                                }
                            });
                        }
                    });
                }
            });
        }
    });

    $("button[value='saveAllParameter']").click(function () {
        const deleteBtns = document.querySelectorAll('#tableElementParameters tbody button[value="deleteParameter"]');
        const ids = Array.from(deleteBtns).map(btn => btn.id);
        let variantenID = $('#variante').val();

        ids.forEach(function (id) {
            let wertElement = $("#Wert_" + id);
            let einheitElement = $("#Einheit_" + id);
            let wert = wertElement.val();
            let einheit = einheitElement.val();

            if (id !== "") {
                $.ajax({
                    url: "updateParameter.php",
                    data: {
                        "parameterID": id,
                        "wert": wert,
                        "einheit": einheit,
                        "variantenID": variantenID
                    },
                    type: "GET",
                    success: function (data) {
                        makeToaster(data.trim(), true);
                    }
                });
            }
        });
    });

    //Parameter von Variante entfernen
    $("button[value='deleteParameter']").click(function () {
        if (confirm("Parameter wirklich löschen?")) {
            $('#variantenParameterCh .xxx').remove();
            let variantenID = $('#variante').val();
            let id = this.id;
            if (id !== "") {
                $.ajax({
                    url: "deleteParameterFromVariante.php",
                    data: {"parameterID": id, "variantenID": variantenID},
                    type: "GET",
                    success: function (data) {
                        makeToaster(data.trim(), false);
                        $.ajax({
                            url: "getVarianteParameters.php",
                            data: {"variantenID": variantenID},
                            type: "GET",
                            success: function (data) {
                                $('#variantenParameterCh .xxx').remove();
                                $("#variantenParameter").html(data);
                                $.ajax({
                                    url: "getPossibleVarianteParameters.php",
                                    data: {"variantenID": variantenID},
                                    type: "GET",
                                    success: function (data) {
                                        $("#possibleVariantenParameter").html(data);
                                        //console.log("2", data);
                                    }
                                });
                            }
                        });
                    }
                });
            }
        }
    });

    // Parameter ändern bzw speichern


    // Variantenparameter übernehmen in zentrales Element
    $("#addVariantenParameterToElement").click(function () {
        const username = "  <?php echo $_SESSION["username"] ?>";
        const elementID = <?php echo $_SESSION["elementID"] ?>;
        const variantenID = <?php echo $_SESSION["variantenID"] ?>;
        console.log(username.trim());
        if (username.toLowerCase().trim() === "reuther") { // } || username.toLowerCase().trim() === "fuchs") {
            $.ajax({
                url: "addVariantenParameterToElement.php",
                data: {"elementID": elementID, "variantenID": variantenID},
                type: "GET",
                success: function (data) {
                    makeToaster(data.trim(), true);  //alert(data);
                    $.ajax({
                        url: "getStandardElementParameters.php",
                        data: {"elementID": elementID},
                        type: "GET",
                        success: function (data) {
                            $("#elementDBParameter").html(data);
                        }
                    });
                }
            });
        }
    });
</script>
</body>
</html>