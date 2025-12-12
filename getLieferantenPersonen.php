<?php
// 25 FX
require_once "utils/_utils.php";
check_login();

$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen,
       tabelle_ansprechpersonen.Name,
       tabelle_ansprechpersonen.Vorname,
       tabelle_ansprechpersonen.Tel,
       tabelle_ansprechpersonen.Adresse,
       tabelle_ansprechpersonen.PLZ,
       tabelle_ansprechpersonen.Ort,
       tabelle_ansprechpersonen.Land,
       tabelle_ansprechpersonen.Mail,
       tabelle_lieferant.Lieferant,
       tabelle_abteilung.Abteilung,
       tabelle_lieferant.idTABELLE_Lieferant,
       tabelle_abteilung.idtabelle_abteilung,
       tabelle_ansprechpersonen.Gebietsbereich
FROM tabelle_abteilung
         INNER JOIN (tabelle_lieferant INNER JOIN tabelle_ansprechpersonen ON tabelle_lieferant.idTABELLE_Lieferant =
                                                                              tabelle_ansprechpersonen.tabelle_lieferant_idTABELLE_Lieferant)
                    ON tabelle_abteilung.idtabelle_abteilung =
                       tabelle_ansprechpersonen.tabelle_abteilung_idtabelle_abteilung";

$result = $mysqli->query($sql);

echo "<table class='table table-striped table-bordered nowrap table-condensed' id='tableLieferanten'>
                <thead><tr>
                <th>ID</th>
                <th>Name</th>
                <th>Vorname</th>
                <th>Tel</th>
                <th>Mail</th>
                <th>Adresse</th>
                <th>PLZ</th>
                <th>Ort</th>
                <th>Land</th>
                <th>Lieferant</th>
                <th>Abteilung</th>
                <th>Gebiet</th>
                <th></th>
                <th></th>
                <th></th>
                </tr></thead><tbody>";


while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["idTABELLE_Ansprechpersonen"] . "</td>";
    echo "<td>" . $row["Name"] . "</td>";
    echo "<td>" . $row["Vorname"] . "</td>";
    echo "<td>" . $row["Tel"] . "</td>";
    echo "<td>" . $row["Mail"] . "</td>";
    echo "<td>" . $row["Adresse"] . "</td>";
    echo "<td>" . $row["PLZ"] . "</td>";
    echo "<td>" . $row["Ort"] . "</td>";
    echo "<td>" . $row["Land"] . "</td>";
    echo "<td>" . $row["Lieferant"] . "</td>";
    echo "<td>" . $row["Abteilung"] . "</td>";
    echo "<td>" . $row["Gebietsbereich"] . "</td>";
    echo "<td><button type='button' id='" . $row["idTABELLE_Ansprechpersonen"] . "' class='btn btn-default btn-sm' value='changeContact' data-bs-toggle='modal' data-bs-target='#addContactModal'><span class='glyphicon glyphicon-pencil'></span></button></td>";
    echo "<td>" . $row["idTABELLE_Lieferant"] . "</td>";
    echo "<td>" . $row["idtabelle_abteilung"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";
echo "< type='button' id='addContactModalButton' class='btn btn-success btn-sm' value='Lieferantenkontakt hinzufügen' data-bs-toggle='modal' data-bs-target='#addContactModal'>";

require_once "modal_LieferantenKontaktHinzufuegen.php";
?>



<script>
    $(document).ready(function () {
        let table1 = $('#tableLieferanten').DataTable({
            columnDefs: [
                {
                    targets: [0, 13, 14],
                    visible: false,
                    searchable: false
                }
            ],
            paging: true,
            searching: true,
            info: true,
            order: [[1, 'asc']],
            pagingType: 'simple_numbers',
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'
            }
        });


        $('#tableLieferanten tbody').on('click', 'tr', function () {
            if ($(this).hasClass('info')) {
            } else {
                table1.$('tr.info').removeClass('info');
                $(this).addClass('info');
            }
            ansprechID = table1.row($(this)).data()[0];
            document.getElementById("lieferantenName").value = table1.row($(this)).data()[1];
            document.getElementById("lieferantenVorname").value = table1.row($(this)).data()[2];
            document.getElementById("lieferantenTel").value = table1.row($(this)).data()[3];
            document.getElementById("lieferantenAdresse").value = table1.row($(this)).data()[5];
            document.getElementById("lieferantenPLZ").value = table1.row($(this)).data()[6];
            document.getElementById("lieferantenOrt").value = table1.row($(this)).data()[7];
            document.getElementById("lieferantenLand").value = table1.row($(this)).data()[8];
            document.getElementById("lieferantenEmail").value = table1.row($(this)).data()[4];
            document.getElementById("lieferant").value = table1.row($(this)).data()[13];
            document.getElementById("abteilung").value = table1.row($(this)).data()[14];
            document.getElementById("lieferantenGebiet").value = table1.row($(this)).data()[11];
        });
    });


    $("#addLieferantenKontakt").click(function () {
        let Name = $("#lieferantenName").val();
        let Vorname = $("#lieferantenVorname").val();
        let Tel = $("#lieferantenTel").val();
        let Adresse = $("#lieferantenAdresse").val();
        let PLZ = $("#lieferantenPLZ").val();
        let Ort = $("#lieferantenOrt").val();
        let Land = $("#lieferantenLand").val();
        let Email = $("#lieferantenEmail").val();
        let lieferant = $("#lieferant").val();
        let abteilung = $("#abteilung").val();
        let gebiet = $("#lieferantenGebiet").val();
        if (Name.length > 0 && Vorname.length > 0 && Tel.length > 0) {
            $('#addContactModal').modal('hide');
            $.ajax({
                url: "addLieferant.php",
                data: {
                    "Name": Name,
                    "Vorname": Vorname,
                    "Tel": Tel,
                    "Adresse": Adresse,
                    "PLZ": PLZ,
                    "Ort": Ort,
                    "Land": Land,
                    "Email": Email,
                    "lieferant": lieferant,
                    "abteilung": abteilung,
                    "gebiet": gebiet
                },
                type: "POST",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getLieferantenPersonen.php",
                        type: "POST",
                        success: function (data) {
                            $("#lieferanten").html(data);

                        }
                    });
                }
            });
        } else {
            alert("Bitte überprüfen Sie Ihre Angaben! Name, Vorname und Tel ist Pflicht!");
        }
    });


    $("#saveLieferantenKontakt").click(function () {
        let Name = $("#lieferantenName").val();
        let Vorname = $("#lieferantenVorname").val();
        let Tel = $("#lieferantenTel").val();
        let Adresse = $("#lieferantenAdresse").val();
        let PLZ = $("#lieferantenPLZ").val();
        let Ort = $("#lieferantenOrt").val();
        let Land = $("#lieferantenLand").val();
        let Email = $("#lieferantenEmail").val();
        let lieferant = $("#lieferant").val();
        let abteilung = $("#abteilung").val();
        let gebiet = $("#lieferantenGebiet").val();
        if (Name.length > 0 && Vorname.length > 0 && Tel.length > 0) {
            $('#addContactModal').modal('hide');
            $.ajax({
                url: "saveLieferantenKontakt.php",
                data: {
                    "ansprechID": ansprechID,
                    "Name": Name,
                    "Vorname": Vorname,
                    "Tel": Tel,
                    "Adresse": Adresse,
                    "PLZ": PLZ,
                    "Ort": Ort,
                    "Land": Land,
                    "Email": Email,
                    "lieferant": lieferant,
                    "abteilung": abteilung,
                    "gebiet": gebiet
                },
                type: "POST",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getLieferantenPersonen.php",
                        type: "POST",
                        success: function (data) {
                            $("#lieferanten").html(data);
                        }
                    });
                }
            });
        } else {
            alert("Bitte überprüfen Sie Ihre Angaben! Name, Vorname und Tel ist Pflicht!");
        }
    });


    $("#addContactModalButton").click(function () {
        document.getElementById("lieferantenName").value = "";
        document.getElementById("lieferantenVorname").value = "";
        document.getElementById("lieferantenTel").value = "";
        document.getElementById("lieferantenAdresse").value = "";
        document.getElementById("lieferantenPLZ").value = "";
        document.getElementById("lieferantenOrt").value = "";
        document.getElementById("lieferantenLand").value = "";
        document.getElementById("lieferantenEmail").value = "";
        document.getElementById("lieferantenGebiet").value = "";
        document.getElementById("saveLieferantenKontakt").style.display = "none";        // Buttons ein/ausblenden!
        document.getElementById("addLieferantenKontakt").style.display = "inline";
    });

    $("button[value='changeContact']").click(function () {
        document.getElementById("addLieferantenKontakt").style.display = "none";
        document.getElementById("saveLieferantenKontakt").style.display = "inline";
    });
</script>