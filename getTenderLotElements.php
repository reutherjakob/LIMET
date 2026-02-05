<?php
// 26 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$losID = getPostInt('lotID', (int)($_SESSION["lotID"]));
$projectID = getPostInt('projectID', (int)($_SESSION["projectID"]));
if ($losID > 0) {
    $_SESSION["lotID"] = $losID;
}

$stmt = $mysqli->prepare("
    SELECT 
        tabelle_räume_has_tabelle_elemente.id, 
        tabelle_räume_has_tabelle_elemente.Anzahl, 
        tabelle_elemente.ElementID, 
        tabelle_elemente.Bezeichnung AS ElementBezeichnung, 
        tabelle_varianten.Variante, 
        tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, 
        tabelle_räume.Geschoss, 
        tabelle_räume.`Raumbereich Nutzer`,   
        tabelle_räume.Raumnr, 
        tabelle_räume.Raumbezeichnung, 
        tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
        tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, 
        tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
    FROM 
        tabelle_varianten 
        INNER JOIN (
            (tabelle_räume_has_tabelle_elemente 
            INNER JOIN tabelle_räume 
            ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) 
            INNER JOIN tabelle_elemente 
            ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente
        ) 
        ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
    WHERE 
        tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern = ? 
        AND tabelle_räume_has_tabelle_elemente.Standort = 1
    ORDER BY 
        tabelle_räume.Raumnr;
");

$stmt->bind_param("i", $losID);
$stmt->execute();
$result = $stmt->get_result();

echo "<table class='table table-sm table-responsive table-striped table-bordered table-hover border border-light border-5' id='tableLotElements1'>
            <thead><tr>
            <th>ID</th>
            <th>variantenID</th>
            <th>elementID</th>
            <th>Stk</th>
            <th>ID</th>
            <th>Element</th>
            <th>Variante</th>
            <th>Bestand</th>
            <th>Raumnr</th>
            <th>Raum</th>
            <th>Geschoss</th>
            <th>Raumbereich Nutzer</th>
            <th>Kommentar</th>								
            </tr></thead>           
            <tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td>" . $row["tabelle_Varianten_idtabelle_Varianten"] . "</td>";
    echo "<td>" . $row["TABELLE_Elemente_idTABELLE_Elemente"] . "</td>";
    echo "<td>" . $row["Anzahl"] . "</td>";
    echo "<td>" . $row["ElementID"] . "</td>";
    echo "<td>" . $row["ElementBezeichnung"] . "</td>";
    echo "<td>" . $row["Variante"] . "</td>";
    echo "<td>";
    echo match ((int)$row["Neu/Bestand"]) {
        1 => "Nein",
        0 => "Ja"
    };
    echo "</td>";
    echo "<td>" . $row["Raumnr"] . "</td>";
    echo "<td>" . $row["Raumbezeichnung"] . "</td>";
    echo "<td>" . $row["Geschoss"] . "</td>";
    echo "<td>" . $row["Raumbereich Nutzer"] . "</td>         <td>";

    $Kurzbeschreibung = trim($row["Kurzbeschreibung"] ?? "");
    $buttonClass = $Kurzbeschreibung === "" ? "btn-outline-secondary" : "btn-outline-dark";
    $iconClass = $Kurzbeschreibung === "" ? "fa fa-comment-slash" : "fa fa-comment";
    $dataAttr = $Kurzbeschreibung === "" ? "data-description=''" : "data-description='" . htmlspecialchars($Kurzbeschreibung, ENT_QUOTES, 'UTF-8') . "'";

    echo "<button type='button'
    class='btn btn-sm " . $buttonClass . " comment-btn' " . $dataAttr . "
    id='" . $row['id'] . "' title='Kommentar'>
    <i class='" . $iconClass . "'></i>
  </button></td>";
}
echo "</tbody></table>";
$mysqli->close();
?>
<script src="utils/_utils.js"></script>

<script charset="utf-8" type="module">
    const { default: CustomPopover } = await import('./utils/_popover.js');

    var tableLotElements1;
    if (typeof excelfilename2 === "undefined") {
        var excelfilename2;
    }

    if (typeof excelfilename3 === "undefined") {
        var excelfilename3;
    }

    $(document).ready(function () {
        CustomPopover.init('.comment-btn', {
            onSave: (trigger, newText) => {
                trigger.dataset.description = newText;
                const id = trigger.id;

                $.ajax({
                    url: 'saveRoomElementComment.php',
                    data: { comment: newText, id: id },
                    type: 'POST',
                    success: function(data) {
                        const btn = $(`.comment-btn[id='${id}']`);
                        if (newText.trim() === '') {
                            btn.removeClass('btn-outline-dark').addClass('btn-outline-secondary');
                            btn.find('i').removeClass('fa fa-comment').addClass('fa fa-comment-slash');
                        } else {
                            btn.removeClass('btn-outline-secondary').addClass('btn-outline-dark');
                            btn.find('i').removeClass('fa fa-comment-slash').addClass('fa fa-comment');
                        }
                        btn.attr('data-description', newText).data('description', newText);

                        if (typeof makeToaster === 'function') {
                            makeToaster(data.trim(), true);
                        }
                    },
                    error: function() {
                        alert("Fehler beim Speichern des Kommentars.");
                    }
                });
            }
        });

        getExcelFilename('Elemente-im-Los')
            .then(filename => {
                excelfilename2 = filename;
                getExcelFilename('Verortungsliste')
                    .then(filename => {
                        excelfilename3 = filename;
                        tableLotElements1 = new DataTable('#tableLotElements1', {
                            dom: '<"#topDiv.top-container d-flex"<"col-md-6 justify-content-start"B><"#topDivSearch2.col-md-6"f>>t<"bottom d-flex" <"col-md-6 justify-content-start"i><"col-md-6 d-flex align-items-center justify-content-end"lp>>',
                            columnDefs: [
                                {
                                    targets: [0, 1, 2],
                                    visible: false,
                                    searchable: false
                                }
                            ],
                            searching: true,
                            info: true,
                            paging: true,
                            select: true,
                            order: [[3, 'asc']],
                            pagingType: 'simple',
                            lengthChange: false,
                            pageLength: 10,
                            language: {
                                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                                search: "",
                                searchPlaceholder: "Suche..."
                            },
                            buttons: [
                                {
                                    extend: 'excel',
                                    text: ' Excel',
                                    className: "btn btn-success fas fa-file-excel me-1 ms-1",
                                    title: excelfilename2,
                                    exportOptions: {
                                        columns: ':gt(2)',
                                        modifier: {
                                            page: 'all'
                                        }
                                    }
                                },
                                {
                                    text: 'Elementliste PDF',
                                    className: "btn btn-md bg-white btn-outline-secondary fas fa-file-pdf me-1 ms-1",
                                    action: function () {
                                        var lotID = <?php echo json_encode($losID); ?>;
                                        var projectID = <?php echo json_encode($projectID); ?>;

                                        var form = $('<form>', {
                                            'method': 'POST',
                                            'action': 'PDFs/pdf_createLotElementListPDF.php',
                                            'target': '_blank',
                                            'style': 'display: none;'
                                        }).appendTo('body');

                                        form.append($('<input>', {
                                            'type': 'hidden',
                                            'name': 'lotID',
                                            'value': lotID
                                        }));

                                        form.append($('<input>', {
                                            'type': 'hidden',
                                            'name': 'projectID',
                                            'value': projectID
                                        }));

                                        form.submit();
                                        form.remove();
                                    }
                                }
                            ]
                        });

                        $('#tableLotElements1 tbody').on('click', 'tr', function () {
                            let elementID = tableLotElements1.row($(this)).data()[2];
                            let variantenID = tableLotElements1.row($(this)).data()[1];
                            let id = tableLotElements1.row($(this)).data()[0];
                            let stk = tableLotElements1.row($(this)).data()[3];
                            $.ajax({
                                url: "getVariantenParameters.php",
                                data: {"variantenID": variantenID, "elementID": elementID},
                                type: "POST",
                                success: function (data) {
                                    $("#elementsvariantenParameterInLot").html(data);
                                    $("#elementsvariantenParameterInLot").show();
                                    $.ajax({
                                        url: "getElementBestand.php",
                                        data: {"id": id, "stk": stk},
                                        type: "POST",
                                        success: function (data) {
                                            $("#elementBestand").html(data);
                                            $("#elementBestand").show();
                                        }
                                    });
                                }
                            });
                        });
                    });
            });
    });
</script>