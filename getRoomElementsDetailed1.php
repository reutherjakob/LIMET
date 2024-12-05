<?php
// V2.0: 2024-11-29, Reuther & Fux
include '_utils.php';
include "_format.php";
check_login();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <style>
        /* .popover-content {
            height: 200px;
            width: 200px;
        }

        textarea.popover-textarea {
            border: 1px;
            margin: 0px;
            width: 100%;
            height: 200px;
            padding: 0px;
            box-shadow: none;
        }

        .popover-footer {
            margin: 0;
            padding: 8px 14px;
            font-size: 14px;
            font-weight: 400;
            line-height: 18px;
            background-color: #F7F7F7;
            border-bottom: 1px solid #EBEBEB;
            border-radius: 5px 5px 0 0;
        }

        .input-xs {
            height: 22px;
            padding: 2px 5px;
            font-size: 12px;
            line-height: 1.5;
            border-radius: 3px;
        } */

    </style>
</head>
<body>

<?php
$mysqli = utils_connect_sql();

$sql = "SELECT Sum(`tabelle_räume_has_tabelle_elemente`.`Anzahl`*`tabelle_projekt_varianten_kosten`.`Kosten`) AS Summe_Neu
                FROM tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_projekt_varianten_kosten ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)
                WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" . $_SESSION["roomID"] . ") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=1));";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
$SummeNeu = (float)$row["Summe_Neu"];
$formattedNumber = format_money_report($row["Summe_Neu"]);
echo "<form class='form-inline'>

            <div class='form-group'>
                <label for='kosten_neu'>Raumkosten-Neu: </label>
                <input type='text' class='ml-4 mr-4 form-control input-xs' id='kosten_neu' value= '$formattedNumber' disabled='disabled'></input>
            </div>";

// Raumkosten berechnen Bestand-Elemente
$sql = "SELECT Sum(`tabelle_räume_has_tabelle_elemente`.`Anzahl`*`tabelle_projekt_varianten_kosten`.`Kosten`) AS Summe_Bestand
                FROM tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_projekt_varianten_kosten ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)
                WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" . $_SESSION["roomID"] . ") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=0));";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
$formattedNumber = format_money_report($row["Summe_Bestand"]);

echo "<div class='form-group'>
                    <label for='kosten_neu'>Raumkosten-Bestand: </label>
                    <input type='text' class='ml-4 form-control input-xs' id='kosten_neu' value='$formattedNumber' disabled='disabled'></input>
                </div>
           </div>";
$Summe = (float)$row["Summe_Bestand"] + $SummeNeu;
$formattedNumber = format_money_report($Summe);
echo "<div class='form-group'>
                    <label for='kosten_neu'>Gesammt: </label>
                    <input type='text' class='ml-4 form-control input-xs' id='kosten_neu' value='$formattedNumber' disabled='disabled'></input>
                </div>						  			 											 						 			
           </div>	
           </form>";

//Elemente im Raum abfragen
$sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete,
       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
       tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.Anzahl, 
       tabelle_elemente.ElementID, tabelle_elemente.Kurzbeschreibung As `Elementbeschreibung`, tabelle_varianten.Variante, 
       tabelle_elemente.Bezeichnung, tabelle_geraete.GeraeteID, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, 
       tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort, 
       tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, 
       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
       tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete
			FROM tabelle_varianten INNER JOIN (tabelle_hersteller RIGHT JOIN ((tabelle_räume_has_tabelle_elemente LEFT JOIN tabelle_geraete ON tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete = tabelle_geraete.idTABELLE_Geraete) INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
			WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" . $_SESSION["roomID"] . "))
			ORDER BY tabelle_elemente.ElementID;";

$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
    echo "<input type='button' class='mt-4 btn btn-outline-dark btn-xs' value='Rauminhalt kopieren' id='" . $_SESSION["roomID"] . "' data-toggle='modal' data-target='#copyRoomElementsModal'></input>";
    echo "<button type='button' class='mt-4 ml-4 btn btn-outline-dark btn-xs' value='createRoombookPDF' id='" . $_SESSION["roomID"] . "'><i class='far fa-file-pdf'></i> Raumbuch-PDF</button>";
    echo "<button type='button' class='mt-4 ml-4 btn btn-outline-dark btn-xs' value='createRoombookPDFCosts' id='" . $_SESSION["roomID"] . "'><i class='far fa-file-pdf'></i> Raumbuch-PDF-Kosten</button>";
}


echo "
            <table class='table table-striped table-bordered table-sm' id='tableRoomElements'  style='width:100%'>
	<thead><tr>
	<th>ID</th>  
	<th>Element</th>
	<th>Var</th>
	<th>Stk</th>
	<th>Best</th>
	<th>Stand</th>
	<th>Verw</th>
	<th>Kommentar</th>
    <th>Verlauf</th>
	<th></th> 
	</tr></thead>
	<tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td> <span id='ElementName" . $row["id"] . "'>" . $row["ElementID"] . " " . $row["Bezeichnung"] . " </span> </td>";
    echo "<td>
	    	<select class='form-control form-control-sm' id='variante" . $row["id"] . "'>";

    $selectedOption = $row["tabelle_Varianten_idtabelle_Varianten"];
    $options = [
        1 => 'A',
        2 => 'B',
        3 => 'C',
        4 => 'D',
        5 => 'E',
        6 => 'F',
        7 => 'G'
    ];

    foreach ($options as $value => $label) {
        $selected = ($selectedOption == $value) ? "selected" : "";
        echo "<option value='$value' $selected>$label</option>";
    }

    echo "</select></td>";
    echo "<td><input class='form-control form-control-sm' type='text' id='amount" . $row["id"] . "' value='" . $row["Anzahl"] . "' size='2'></input></td>";
    echo "<td>
	    	<select class='form-control form-control-sm' id='bestand" . $row["id"] . "'>";
    if ($row["Neu/Bestand"] == "0") {
        echo "<option value=0 selected>Ja</option>";
        echo "<option value=1>Nein</option>";
    } else {
        echo "<option value=0>Ja</option>";
        echo "<option value=1 selected>Nein</option>";
    }
    echo "</select></td>";
    echo "<td>   	
                <select class='form-control form-control-sm' id='Standort" . $row["id"] . "'>";
    if ($row["Standort"] == "0") {
        echo "<option value=0 selected>Nein</option>";
        echo "<option value=1>Ja</option>";
    } else {
        echo "<option value=0>Nein</option>";
        echo "<option value=1 selected>Ja</option>";
    }
    echo "</select></td>";
    echo "<td>   	    	
                        <select class='form-control form-control-sm' id='Verwendung" . $row["id"] . "'>";
    if ($row["Verwendung"] == "0") {
        echo "<option value=0 selected>Nein</option>";
        echo "<option value=1>Ja</option>";
    } else {
        echo "<option value=0>Nein</option>";
        echo "<option value=1 selected>Ja</option>";
    }
    echo "</select></td>";

    if (null != ($row["Kurzbeschreibung"])) {
        echo "<td><button type='button' class='btn btn-xs btn-outline-dark' id='buttonComment" . $row["id"] . "' name='showComment' value='" . $row["Kurzbeschreibung"] . "' title='Kommentar'><i class='fa fa-comment'></i></button></td>";
    } else {
        echo "<td><button type='button' class='btn btn-xs btn-outline-dark' id='buttonComment" . $row["id"] . "' name='showComment' value='" . $row["Kurzbeschreibung"] . "' title='Kommentar'><i class='fa fa-comment-slash'></i></button></td>";
    }
    echo "<td><button type='button' id='" . $row["id"] . "' class='btn btn-xs btn-outline-dark' value='history'><i class='fas fa-history'></i></button></td>";
    echo "<td><button type='button' id='" . $row["id"] . "' class='btn btn-xs btn-warning' value='saveElement'><i class='far fa-save'></i></button></td>";
    echo "<td>" . $row["TABELLE_Elemente_idTABELLE_Elemente"] . "</td>";
    echo "</tr>";
}

echo "</tbody></table>";

$mysqli->close();
?>

<!-- Modal zum Kopieren des Rauminhalts -->
<div class='modal fade' id='copyRoomElementsModal' role='dialog'>
    <div class='modal-dialog modal-lg'>

        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Rauminhalt kopieren</h4>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
            </div>
            <div class='modal-footer'>
                <input type='button' id='copyRoomElements' class='btn btn-info btn-sm'
                       value='Elemente kopieren'></input>
                <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Close</button>
            </div>
        </div>

    </div>
</div>

<!-- Modal zum Darstellen des Verlaufs -->
<div class='modal fade' id='historyModal' role='dialog'>
    <div class='modal-dialog modal-lg'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Verlauf</h4>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbodyHistory'>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Info -->
<div class='modal fade' id='infoModal' role='dialog'>
    <div class='modal-dialog modal-sm'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Info</h4>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='infoBody'>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>OK</button>
            </div>
        </div>

    </div>
</div>

<script src="_utils.js"></script>
<script>

    $("button[value='saveElement']").click(function () {
        var id = this.id;
        var comment = $("#buttonComment" + id).val();
        var amount = $("#amount" + id).val();
        var variantenID = $("#variante" + id).val();
        var bestand = $("#bestand" + id).val();
        var standort = $("#Standort" + id).val();
        var verwendung = $("#Verwendung" + id).val();
        var ElementName = $("#ElementName" + id).text();

        if (standort === '0' && verwendung === '0') {
            alert("Standort und Verwendung kann nicht Nein sein!");
        } else {
            $.ajax({
                url: "saveRoombookEntry.php",
                data: {
                    "comment": comment,
                    "id": id,
                    "amount": amount,
                    "variantenID": variantenID,
                    "bestand": bestand,
                    "standort": standort,
                    "verwendung": verwendung
                },
                type: "GET",
                success: function (data) {
                    //alert(data);    data + $("#infoBody").html(data);  $('#infoModal').modal('show');
                    makeToaster(data.trim(), true);
                }
            });
        }
    });

    $(document).ready(function () {
        $("#tableRoomElements").DataTable({
            "select": true,
            "paging": true,
            "pagingType": "simple",
            "lengthChange": false,
            "pageLength": 10,
            "searching": true,
            "info": true,
            "columnDefs": [
                {
                    "targets": [0, 10],
                    "visible": false,
                    "searchable": false,
                    "sortable": false
                },
                {
                    "targets": [3, 4, 5, 6, 7, 8, 9],
                    "searchable": false,

                }
            ],
            "order": [[1, "asc"]],
            "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
        });

        var table = $('#tableRoomElements').DataTable();
        $('#tableRoomElements tbody').on('click', 'tr', function () {
            var id = table.row($(this)).data()[0];
            var stk = $("#amount" + id).val();
            var standort = $("#Standort" + id).val();
            var verwendung = $("#Verwendung" + id).val();
            var elementID = table.row($(this)).data()[10];

            $.ajax({
                url: "getElementParameters.php",
                data: {"id": id},
                type: "GET",
                success: function (data) {
                    $("#elementParameters").html(data);
                    $("#elementParameters").show();
                    $.ajax({
                        url: "getElementPrice.php",
                        data: {"id": id},
                        type: "GET",
                        success: function (data) {
                            $("#price").html(data);
                            $.ajax({
                                url: "getElementBestand.php",
                                data: {"id": id, "stk": stk},
                                type: "GET",
                                success: function (data) {
                                    $("#elementBestand").html(data);
                                    $("#elementBestand").show();
                                    if (standort === '0' && verwendung === '1') {
                                        $.ajax({
                                            url: "getElementStandort.php",
                                            data: {"id": id, "elementID": elementID},
                                            type: "GET",
                                            success: function (data) {
                                                $("#elementVerwendung").html(data);
                                                $("#elementVerwendung").show();
                                            }
                                        });
                                    } else {
                                        $("#elementBestand").show();
                                        $.ajax({
                                            url: "getElementVerwendung.php",
                                            data: {"id": id},
                                            type: "GET",
                                            success: function (data) {
                                                $("#elementVerwendung").html(data);
                                                $("#elementVerwendung").show();
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

        /* $("button[name='showComment']").popover({
            trigger: 'click',
            placement: 'right',
            html: true,
            container: 'body',
            content: "<textarea class='popover-textarea'></textarea>",
            template: "<div class='popover'>" +
                "<h4 class='popover-header'></h4><div class='popover-body'>" +
                "</div><div class='popover-footer'><button type='button' class='btn btn-xs btn-outline-dark popover-submit'><i class='fas fa-check'></i>" +
                "</button>&nbsp;" +
                "</div>"
        });

        $("button[name='showComment']").click(function () {
            //hide any visible comment-popover
            $("button[name='showComment']").not(this).popover('hide');
            var id = this.id;
            var val = document.getElementById(id).value;
            //attach/link text
            $('.popover-textarea').val(val).focus();
            //update link text on submit
            $('.popover-submit').click(function () {
                document.getElementById(id).value = $('.popover-textarea').val();
                $(this).parents(".popover").popover('hide');
            });
        });*/
    });

    $("input[value='Rauminhalt kopieren']").click(function () {
        var ID = this.id;

        $.ajax({
            url: "getRoomsToCopy.php",
            type: "GET",
            data: {"id": ID},
            success: function (data) {
                $("#mbody").html(data);
            }
        });
    });

    $("button[value='history']").click(function () {
        var roombookID = this.id;
        $.ajax({
            url: "getCommentHistory.php",
            type: "GET",
            data: {"roombookID": roombookID},
            success: function (data) {
                $("#mbodyHistory").html(data);
                $("#historyModal").modal('show');

            }
        });
    });

    $("button[value='createRoombookPDF']").click(function () {
        window.open('/pdf_createRoombookPDF.php?roomID=' + this.id);//there are many ways to do this
    });

    $("button[value='createRoombookPDFCosts']").click(function () {
        window.open('/pdf_createRoombookPDFwithCosts.php?roomID=' + this.id);//there are many ways to do this
    });


</script>

</body>
</html>