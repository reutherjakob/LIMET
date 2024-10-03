<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <head>
        <style>
            .popover-content {
                height: 180px;
                width: 200px;
            }

            textarea.popover-textarea {
                border: 0px;
                margin: 0px;
                width: 100%;
                height: 100%;
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
                line-height: 1.5; /* If Placeholder of the input is moved up, rem/modify this. */
                border-radius: 3px;
            }
        </style>

    </head>
    <body>
        <?php
        if (!isset($_SESSION["username"])) {
            echo "Bitte erst <a href=\"index.php\">einloggen</a>";
            exit;
        }
        ?>

        <?php
        $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');

        /* change character set to utf8 */
        if (!$mysqli->set_charset("utf8")) {
            printf("Error loading character set utf8: %s\n", $mysqli->error);
            exit();
        }

        $sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
			FROM tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
			WHERE ( ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=" . filter_input(INPUT_GET, 'bestand') . ") AND ((tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)=" . filter_input(INPUT_GET, 'variantenID') . ") AND ((tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)=" . filter_input(INPUT_GET, 'elementID') . ") AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
			ORDER BY tabelle_räume.Raumnr;";

        $result = $mysqli->query($sql);
        echo "<table class='table table-striped table-bordered table-sm' id='tableRoomsWithElement' cellspacing='0' width='100%'>
	<thead><tr>
        <th>ID</th>
	<th>Anzahl</th>
	<th>Variante</th>
	<th>Raumnummer</th>
	<th>Raumbezeichnung</th>
	<th>Raumbereich</th>
	<th>Bestand</th>
	<th>Standort</th>
	<th>Verwendung</th>
	<th></th>
        <th></th>
        <th>Excel Variante</th>
        <th>Excel Standort</th>
        <th>Excel Verwendung</th>
        <th>Excel Bestand</th>
        <th>Excel Anzahl</th>
	</tr></thead><tbody>";

        while ($row = $result->fetch_assoc()) {

            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td><input class='form-control form-control-sm' type='text' id='amount" . $row["id"] . "' value='" . $row["Anzahl"] . "' size='2'></input></td>";
            echo "<td>
   	    	<select class='form-control form-control-sm'' id='variante" . $row["id"] . "'>";
            switch ($row["tabelle_Varianten_idtabelle_Varianten"]) {
                case "1":
                    echo "<option value='1' selected>A</option>
										    <option value='2'>B</option>
										    <option value='3'>C</option>
										    <option value='4'>D</option>
										    <option value='5'>E</option>
										    <option value='6'>F</option>
										    <option value='7'>G</option>";
                    break;
                case "2":
                    echo "<option value='1'>A</option>
										    <option value='2' selected>B</option>
										    <option value='3'>C</option>
										    <option value='4'>D</option>
										    <option value='5'>E</option>
										    <option value='6'>F</option>
										    <option value='7'>G</option>";
                    break;
                case "3":
                    echo "<option value='1'>A</option>
										    <option value='2'>B</option>
										    <option value='3' selected>C</option>
										    <option value='4'>D</option>
										    <option value='5'>E</option>
										    <option value='6'>F</option>
										    <option value='7'>G</option>";
                    break;
                case "4":
                    echo "<option value='1'>A</option>
										    <option value='2'>B</option>
										    <option value='3'>C</option>
										    <option value='4' selected>D</option>
										    <option value='5'>E</option>
										    <option value='6'>F</option>
										    <option value='7'>G</option>";
                    break;
                case "5":
                    echo "<option value='1'>A</option>
										    <option value='2'>B</option>
										    <option value='3'>C</option>
										    <option value='4'>D</option>
										    <option value='5' selected>E</option>
										    <option value='6'>F</option>
										    <option value='7'>G</option>";
                    break;
                case "6":
                    echo "<option value='1'>A</option>
										    <option value='2'>B</option>
										    <option value='3'>C</option>
										    <option value='4'>D</option>
										    <option value='5'>E</option>
										    <option value='6' selected>F</option>
										    <option value='7'>G</option>";
                    break;
                case "7":
                    echo "<option value='1'>A</option>
										    <option value='2'>B</option>
										    <option value='3'>C</option>
										    <option value='4'>D</option>
										    <option value='5'>E</option>
										    <option value='6'>F</option>
										    <option value='7' selected>G</option>";
                    break;
            }
            echo "</select></td>";
            echo "<td>" . $row["Raumnr"] . "</td>";
            echo "<td>" . $row["Raumbezeichnung"] . "</td>";
            echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";

            echo "<td>
	    	<select class='form-control form-control-sm'' id='bestand" . $row["id"] . "'>";
            if ($row["Neu/Bestand"] == "0") {
                echo "<option value=0 selected>Ja</option>";
                echo "<option value=1>Nein</option>";
            } else {
                echo "<option value=0>Ja</option>";
                echo "<option value=1 selected>Nein</option>";
            }
            echo "</select></td>";
            echo "<td>   	
                <select class='form-control form-control-sm'' id='Standort" . $row["id"] . "'>";
            if ($row["Standort"] == "0") {
                echo "<option value=0 selected>Nein</option>";
                echo "<option value=1>Ja</option>";
            } else {
                echo "<option value=0>Nein</option>";
                echo "<option value=1 selected>Ja</option>";
            }
            echo "</select></td>";
            echo "<td>   	    	
                        <select class='form-control form-control-sm'' id='Verwendung" . $row["id"] . "'>";
            if ($row["Verwendung"] == "0") {
                echo "<option value=0 selected>Nein</option>";
                echo "<option value=1>Ja</option>";
            } else {
                echo "<option value=0>Nein</option>";
                echo "<option value=1 selected>Ja</option>";
            }
            echo "</select></td>";
            if (strlen($row["Kurzbeschreibung"]) > 0) {
                echo "<td><button type='button' class='btn btn-xs btn-outline-dark' id='buttonComment" . $row["id"] . "' name='showComment' value='" . $row["Kurzbeschreibung"] . "' title='Kommentar'><i class='fa fa-comment'></i></button></td>";
            } else {
                echo "<td><button type='button' class='btn btn-xs btn-outline-dark' id='buttonComment" . $row["id"] . "' name='showComment' value='" . $row["Kurzbeschreibung"] . "' title='Kommentar'><i class='fa fa-comment-slash'></i></button></td>";
            }
            echo "<td><button type='button' id='" . $row["id"] . "' class='btn btn-warning btn-xs' value='saveElement'><i class='far fa-save'></i></button></td>";
            echo "<td>" . $row["tabelle_Varianten_idtabelle_Varianten"] . "</td>";
            echo "<td>" . $row["Standort"] . "</td>";
            echo "<td>" . $row["Verwendung"] . "</td>";
            echo "<td>" . $row["Neu/Bestand"] . "</td>";
            echo "<td>" . $row["Anzahl"] . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        $mysqli->close();
        echo "<!-- Modal --> <!-- data-toggle='modal' data-target='#myModal' --> 
                <div class='modal fade' id='myModal' role='dialog'>
                  <div class='modal-dialog'>

                    <!-- Modal content-->
                    <div class='modal-content'>
                      <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'>&times;</button>
                        <h4 class='modal-title'>Kommentar</h4>
                      </div>
                      <div class='modal-body' id='mbody'>
                              <div class='modal-body-inner'></div>
                      </div>
                      <div class='modal-footer'>
                        <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                      </div>
                    </div>

                  </div>
                </div>";
        ?>
        <<script src="_utils.js"></script>
        <script>

            $(document).ready(function () {
                $('#tableRoomsWithElement').DataTable({
                    "columnDefs": [
                        {
                            "targets": [0, 11, 12, 13, 14, 15],
                            "visible": false,
                            "searchable": false
                        }],
                    "paging": false,
                    "searching": true,
                    "info": false,
                    "order": [[1, "asc"]],
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                    dom: '<"top"Blf>rt<"bottom"><"clear">',
                    "buttons": [
                        {
                            extend: 'excel',
                            exportOptions: {
                                columns: [15, 11, 3, 4, 5, 12, 14]
                            }
                        }
                    ]
                });

                var table = $('#tableRoomsWithElement').DataTable();
                $('#tableRoomsWithElement tbody').on('click', 'tr', function () {
                    if ($(this).hasClass('info')) {
                        //$(this).removeClass('info');
                    } else {
                        table.$('tr.info').removeClass('info');
                        $(this).addClass('info');
                    }
                });


                // Popover for Comment            
                $("button[name='showComment']").popover({
                    trigger: 'click',
                    placement: 'top',
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
                });
            });


            // Element speichern
            $("button[value='saveElement']").click(function () {
                var id = this.id;
                var comment = $("#buttonComment" + id).val();
                var amount = $("#amount" + id).val();
                var variantenID = $("#variante" + id).val();
                var bestand = $("#bestand" + id).val();
                var standort = $("#Standort" + id).val();
                var verwendung = $("#Verwendung" + id).val();

                if (standort === '0' && verwendung === '0') {
                    alert("Standort und Verwendung kann nicht Nein sein!");
                } else {
                    $.ajax({
                        url: "saveRoombookEntry.php",
                        data: {"comment": comment, "id": id, "amount": amount, "variantenID": variantenID, "bestand": bestand, "standort": standort, "verwendung": verwendung},
                        type: "GET",
                        success: function (data) {
                            //                            alert(data);
                            //                            location.reload(); 
                            makeToaster(data.trim(), true);
                        }
                    });
                }
            });
        </script>

    </body>
</html>