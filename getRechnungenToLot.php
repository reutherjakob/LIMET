<?php
    session_start();
?>

<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>
<body>
    
<?php
    if(!isset($_SESSION["username"]))
    {
        echo "Bitte erst <a href=\"index.php\">einloggen</a>";
        exit;
    }
    
    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');

    /* change character set to utf8 */
    if (!$mysqli->set_charset("utf8")) {
        printf("Error loading character set utf8: %s\n", $mysqli->error);
        exit();
    } 
    

    $sql = "SELECT tabelle_rechnungen.idtabelle_rechnungen, tabelle_rechnungen.Nummer, tabelle_rechnungen.InterneNummer, tabelle_rechnungen.Ausstellungsdatum, tabelle_rechnungen.Eingangsdatum, tabelle_rechnungen.Rechnungssumme, tabelle_rechnungen.Bearbeiter, tabelle_rechnungen.Schlussrechnung, tabelle_rechnungen.tabelle_Files_idtabelle_Files
            FROM tabelle_rechnungen
            WHERE (((tabelle_rechnungen.tabelle_lose_extern_idtabelle_Lose_Extern)=".filter_input(INPUT_GET, 'lotID')."))
            ORDER BY InterneNummer ASC;";
    
    $result = $mysqli->query($sql);

    echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableRechnungen'   >
    <thead><tr>    
    <th>id</th>
    <th>Interne Nr</th>
    <th>Nummer</th>
    <th>Ausstellungsdatum</th> 
    <th>Eingang</th>
    <th>Rechnungssumme-hidden</th>
    <th>Rechnungssumme</th>
    <th>Bearbeiter</th> 
    <th>Schlussrechnung</th>
    <th>SchlussrechnungZahl</th>
    <th>PDF</th>
    </tr></thead><tbody>";

    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row["idtabelle_rechnungen"]."</td>";
        echo "<td>".$row["InterneNummer"]."</td>";
        echo "<td>".$row["Nummer"]."</td>";
        echo "<td>".$row["Ausstellungsdatum"]."</td>"; 
        echo "<td>".$row["Eingangsdatum"]."</td>"; 
        echo "<td style='text-align:right'>".$row["Rechnungssumme"]."</td>"; 
        echo "<td style='text-align:right'>".number_format($row["Rechnungssumme"], 2, ',', ' ')."</td>"; 
        echo "<td>".$row["Bearbeiter"]."</td>"; 
        echo "<td style='text-align:center'>";
            if($row["Schlussrechnung"] === '0'){
                echo "<span class='badge badge-pill badge-light'> Nein </span>";
            }
            else{
                echo "<span class='badge badge-pill badge-success'> Ja </span>";
            }
        echo "</td>";
        echo "<td>".$row["Schlussrechnung"]."</td>"; 
        echo "<td>";
            if($row["tabelle_Files_idtabelle_Files"] === NULL){
                //Upload
                echo "<button type='button' class='btn btn-outline-dark btn-sm' id='uploadRechnung".$row["idtabelle_rechnungen"]."' name='uploadRechnung' value='".$row["idtabelle_rechnungen"]."'><i class='fas fa-upload'></i></button>";
            }
            else{
                //Upload
                echo "<button type='button' class='btn btn-outline-dark btn-sm' id='uploadRechnung".$row["idtabelle_rechnungen"]."' name='uploadRechnung' value='".$row["idtabelle_rechnungen"]."'><i class='fas fa-upload'></i></button>";
                //Download
                echo "<a href='https://limet-rb.com/Dokumente_RB/Rechnungen/Rechnung_".$row["idtabelle_rechnungen"].".pdf' class='btn btn-outline-dark btn-sm' role='button'><i class='fas fa-download'></i></a>";
                //Löschen
                echo "<button type='button' class='btn btn-outline-dark btn-sm' id='deleteRechnung".$row["idtabelle_rechnungen"]."' name='deleteRechnung' value='".$row["idtabelle_rechnungen"]."'><i class='fas fa-trash'></i></button>";
            }
        echo "</td>";
        echo "</tr>";

    }
    echo "</tbody></table>";
    $mysqli ->close();
?>
    
    
<script>
    $(document).ready(function(){	                        
        $('#tableRechnungen').DataTable( {
            "select": true,
            "searching": false,
            "paging": true,
            "pagingType": "simple",
            "lengthChange": false,
            "pageLength": 10,
            "order": [[ 1, "asc" ]],
            "orderMulti": true,
            "language": {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"},
            "columnDefs": [
                            {
                                "targets": [ 0,5, 9 ],
                                "visible": false,
                                "searchable": false
                            }
                        ]
        } );
        
        var table = $('#tableRechnungen').DataTable();
            $('#tableRechnungen tbody').on( 'click', 'tr', function () {
			
            if ( $(this).hasClass('info') ) {
            }
            else {                   
                document.getElementById("rechnungID").value = table.row( $(this) ).data()[0];
                document.getElementById("teilRechnungNr").value = table.row( $(this) ).data()[1]; 
                document.getElementById("rechnungNr").value = table.row( $(this) ).data()[2]; 
                document.getElementById("rechnungAusstellungsdatum").value = table.row( $(this) ).data()[3]; 
                document.getElementById("rechnungEingangsdatum").value = table.row( $(this) ).data()[4]; 
                document.getElementById("rechnungSum").value = table.row( $(this) ).data()[5]; 
                document.getElementById("rechnungBearbeiter").value = table.row( $(this) ).data()[7];                 
                
                if(table.row( $(this) ).data()[9] === "0"){
                    $('#rechnungSchlussrechnung').bootstrapToggle('off'); 
                }
                else{
                    $('#rechnungSchlussrechnung').bootstrapToggle('on');                     
                }
            }
        } );        
    });
    
    
    $("button[name='uploadRechnung']").click(function(){                       
        var id = this.id;                     
        var idRechnung = document.getElementById(id).value; 
        document.getElementById('rechnungIDFile').value = idRechnung;
        $('#uploadRechnungModal').modal('show'); 
    });
    
    $("button[name='deleteRechnung']").click(function(){          
        var id = this.id;                     
        var idRechnung = document.getElementById(id).value;       
        alert(idRechnung);
        $.ajax({
            url : "deleteFileRechnung.php",
            type: "GET",
            data:{"idRechnung":idRechnung},
            success: function(data){
                alert(data);
            }
        });
    });
                
    
</script>
</body>
</html>