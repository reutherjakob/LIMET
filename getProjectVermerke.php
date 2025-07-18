<div class='table-responsive'>
    <table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableProjectVermerke'  >
        <thead>
        <tr>
            <th>ID</th>
            <th>Art</th>
            <th>Name</th>
            <th>Status</th>
            <th>Datum</th>
            <th>Typ</th>
            <th>Zuständig</th>
            <th>Fälligkeit</th>
            <th>Vermerk</th>
            <th>Raum</th>
            <th>Los</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php
        require_once 'utils/_utils.php';
        check_login();
        $mysqli = utils_connect_sql();

        if (filter_input(INPUT_GET, 'filterValue') === '1') {
            $sql = "SELECT tabelle_Vermerkgruppe.Gruppenname, tabelle_Vermerkgruppe.Gruppenart, tabelle_Vermerkgruppe.Ort, tabelle_Vermerkgruppe.Datum, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, tabelle_Vermerke.Faelligkeit, tabelle_Vermerke.Vermerkart, tabelle_Vermerke.Bearbeitungsstatus, tabelle_Vermerke.Vermerktext, tabelle_Vermerke.Erstellungszeit, tabelle_Vermerke.idtabelle_Vermerke
                    FROM (((tabelle_Vermerke LEFT JOIN (tabelle_ansprechpersonen RIGHT JOIN tabelle_Vermerke_has_tabelle_ansprechpersonen ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen) ON tabelle_Vermerke.idtabelle_Vermerke = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_Vermerke_idtabelle_Vermerke) INNER JOIN (tabelle_Vermerkgruppe INNER JOIN tabelle_Vermerkuntergruppe ON tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe = tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe) ON tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe) LEFT JOIN tabelle_räume ON tabelle_Vermerke.tabelle_räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) LEFT JOIN tabelle_lose_extern ON tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern = tabelle_lose_extern.idtabelle_Lose_Extern
                    WHERE (((tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND tabelle_Vermerke.Vermerkart='Bearbeitung' AND tabelle_Vermerke.Bearbeitungsstatus='0')
                    ORDER BY tabelle_Vermerkgruppe.Datum DESC , tabelle_Vermerke.Erstellungszeit DESC;";
        } else {
            $sql = "SELECT tabelle_Vermerkgruppe.Gruppenname, tabelle_Vermerkgruppe.Gruppenart, tabelle_Vermerkgruppe.Ort, tabelle_Vermerkgruppe.Datum, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, tabelle_Vermerke.Faelligkeit, tabelle_Vermerke.Vermerkart, tabelle_Vermerke.Bearbeitungsstatus, tabelle_Vermerke.Vermerktext, tabelle_Vermerke.Erstellungszeit, tabelle_Vermerke.idtabelle_Vermerke
                FROM (((tabelle_Vermerke LEFT JOIN (tabelle_ansprechpersonen RIGHT JOIN tabelle_Vermerke_has_tabelle_ansprechpersonen ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen) ON tabelle_Vermerke.idtabelle_Vermerke = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_Vermerke_idtabelle_Vermerke) INNER JOIN (tabelle_Vermerkgruppe INNER JOIN tabelle_Vermerkuntergruppe ON tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe = tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe) ON tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe) LEFT JOIN tabelle_räume ON tabelle_Vermerke.tabelle_räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) LEFT JOIN tabelle_lose_extern ON tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern = tabelle_lose_extern.idtabelle_Lose_Extern
                WHERE (((tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                ORDER BY tabelle_Vermerkgruppe.Datum DESC , tabelle_Vermerke.Erstellungszeit DESC;";
        }

        $result = $mysqli->query($sql);

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["idtabelle_Vermerke"] . "</td>";
            echo "<td>" . $row["Gruppenart"] . "</td>";
            echo "<td>" . $row["Gruppenname"] . "</td>";
            echo "<td>";
            if ($row["Vermerkart"] != "Info") {
                if ($row["Bearbeitungsstatus"] == "0") {
                    echo "<div class='form-check form-check-inline'><label class='form-check-label' for='" . $row["idtabelle_Vermerke"] . "'><input type='checkbox' class='form-check-input' id='" . $row["idtabelle_Vermerke"] . "' value='statusCheck'></label></div>";
                } else {
                    echo "<div class='form-check form-check-inline'><label class='form-check-label' for='" . $row["idtabelle_Vermerke"] . "'><input type='checkbox' class='form-check-input' id='" . $row["idtabelle_Vermerke"] . "' value='statusCheck' checked='checked'></label></div>";
                }
            }
            echo "</td>";
            echo "<td>" . $row["Datum"] . "</td>";
            echo "<td>" . $row["Vermerkart"] . "</td>";
            echo "<td>" . $row["Name"] . " " . $row["Vorname"] . "</td>";
            echo "<td>";
            if ($row["Vermerkart"] != "Info") {
                echo $row["Faelligkeit"];
            }
            echo "</td>";
            echo "<td>";
            echo '<button type="button" class="btn btn-sm btn-light vermerk-popover" data-bs-toggle="popover" data-placement="right" data-vermerk-id="'.  $row["idtabelle_Vermerke"] . '" data-bs-content="' . htmlspecialchars($row["Vermerktext"], ENT_QUOTES). '"><i class="far fa-comment"></i></button>';
            echo "</td>";
            echo "<td>" . $row["Raumnr"] . " " . $row["Raumbezeichnung"] . "</td>";
            echo "<td>" . $row["LosNr_Extern"] . "</td>";
            echo "<td>" . $row["Bearbeitungsstatus"] . "</td>";
            echo "</tr>";
        }
        $mysqli->close(); ?>
        </tbody>
    </table>
</div>
<script charset="utf-8">
    $(document).ready(function () {
        $('#tableProjectVermerke').DataTable({
            columnDefs: [
                {
                    targets: [0, 11],
                    visible: false,
                    searchable: false
                }
            ],
            select: true,
            paging: true,
            pagingType: 'simple',
            lengthChange: false,
            pageLength: 10,
            searching: true,
            info: true,
            order: [[4, 'desc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: ''
            },
            rowCallback: function (row, data, index) {
                if (data[5] === 'Bearbeitung') {
                    if (data[11] === '0') {
                        $(row).find('td').css('background-color', '#ff8080');
                    } else {
                        $(row).find('td').css('background-color', '#b8dc6f');
                    }
                } else {
                    $(row).find('td').css('background-color', '#d3edf8');
                }
            }
        });


        // POPOVER READ ONLY
        $('#tableProjectVermerke').on('draw.dt', function () {
            $('.vermerk-popover').popover('dispose').popover({
                html: true,
                content: function () {
                    let content = $(this).attr('data-bs-content');
                    return '<div class="popover-content">' + content + '</div>';

                },
                placement: 'right'
            });

        });

        $(document).on('click', function (e) {
            if ($(e.target).closest('.popover').length === 0 && !$(e.target).hasClass('vermerk-popover')) {
                $('.vermerk-popover').popover('hide');
            }
        });

        $(document).on('click', '.vermerk-popover', function (e) {
            $('.vermerk-popover').not(this).popover('hide');
            $(this).popover('toggle');
            e.stopPropagation();
        });
    });

    $("input[value='statusCheck']").change(function () {
        let vermerkStatus = $(this).prop('checked') ? 1 : 0;
        let vermerkID = this.id;
        if (vermerkStatus !== "" && vermerkID !== "") {
            $.ajax({
                url: "saveVermerkStatus.php",
                data: {"vermerkID": vermerkID, "vermerkStatus": vermerkStatus},
                type: "GET",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getProjectVermerke.php",
                        type: "GET",
                        success: function (data) {
                            $("#projectVermerke").html(data);
                        }
                    });
                }
            });
        } else {
            alert("Vermerkstatus nicht lesbar!");
        }
    });
</script>
