<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<html lang="de">
<head>
    <title>getVGr2Gew</title>
    <style>
    </style>
</head>
<body>
<?php
// 25 FX
require_once "utils/_utils.php";
check_login();

$mysqli = utils_connect_sql();
$vermerkGruppenID = getPostInt('vermerkGruppenID',0);

if (0 != $vermerkGruppenID) {
    echo "Ungültige Eingabe.";
    $mysqli->close();
    exit;
}

$stmt = $mysqli->prepare(
    "SELECT 
        vg.idtabelle_Vermerkgruppe,
        vg.Gruppenname,
        vg.Gruppenart,
        vg.Ort,
        vg.Datum
     FROM tabelle_Vermerkgruppe vg
     INNER JOIN tabelle_Vermerkuntergruppe ug ON vg.idtabelle_Vermerkgruppe = ug.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe
     INNER JOIN tabelle_Vermerke v ON ug.idtabelle_Vermerkuntergruppe = v.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe
     WHERE v.tabelle_lose_extern_idtabelle_Lose_Extern = ?
     GROUP BY vg.idtabelle_Vermerkgruppe, vg.Gruppenname, vg.Gruppenart, vg.Ort, vg.Datum
     HAVING vg.Gruppenart = 'ÖBA-Protokoll'
     ORDER BY vg.Datum"
);

$stmt->bind_param("i", $vermerkGruppenID);
$stmt->execute();
$result = $stmt->get_result();

echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableVermerkgruppen'>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Datum</th>
                <th>Art</th>
                <th>Ort</th>
            </tr>
        </thead>
        <tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['idtabelle_Vermerkgruppe'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td>" . htmlspecialchars($row['Gruppenname']  ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td>" . htmlspecialchars($row['Datum']  ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td>";

    echo match ($row["Gruppenart"]) {
        "Mailverkehr" => "<span class='badge badge-pill badge-info'> Mailverkehr </span>",
        "Telefonnotiz" => "<span class='badge badge-pill badge-dark'> Telefonnotiz </span>",
        "AV" => "<span class='badge badge-pill badge-warning'> AV </span>",
        "Protokoll" => "<span class='badge badge-pill badge-primary'> Protokoll </span>",
        "ÖBA-Protokoll" => "<span class='badge badge-pill badge-success'> ÖBA-Protokoll </span>",
        default => "Art unbekannt: " . htmlspecialchars($row['Gruppenart'] ?? '', ENT_QUOTES, 'UTF-8'),
    };

    echo "</td>";
    echo "<td>" . htmlspecialchars($row['Ort']  ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";
$stmt->close();
$mysqli->close();
?>


<script>
    $(document).ready(function () {
        var table = $('#tableVermerkgruppen').DataTable({
            select: true,
            paging: false,
            pagingType: "simple",
            lengthChange: false,
            pageLength: 20,
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false
                }
            ],
            order: [[2, "asc"]],
            orderMulti: false,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"
            },
            mark: true
        });

        $('#tableVermerkgruppen tbody').on('click', 'tr', function () {
            if (!$(this).hasClass('info')) {
                table.$('tr.info').removeClass('info');
                $(this).addClass('info');
                $('#pdfPreview').attr('src', 'PDFs/pdf_createVermerkGroupPDF.php?gruppenID=' + table.row(this).data()[0]);
            }
        });
    });
</script>
</body>
</html>
