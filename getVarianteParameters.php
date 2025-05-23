<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title>getVarianteParameters</title>
</head>
<body>
<?php
include "_utils.php";
include "getElementParameterTable.php";
check_login();
generate_variante_parameter_inputtable();
?>

<script src="_utils.js"></script>
<script src="saveElementParameters.js"></script>
<script>

    $(document).ready(function () {
        $('#tableElementParameters').DataTable({
            select: true,
            searching: true,
            info: true,
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
                $('#variantenParameterCh .xxx').remove();
                $('#variantenParameter .dt-search label').remove();
                $('#variantenParameter .dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark xxx").appendTo('#variantenParameterCH');
            }
        });
    });

    //Parameter von Variante entfernen
    $("button[value='deleteParameter']").click(function () {
        if (confirm("Parameter wirklich löschen?")) {
            let variantenID = $('#variante').val();
            let id = this.id;
            if (id !== "") {
                $.ajax({
                    url: "deleteParameterFromVariante.php",
                    data: {"parameterID": id, "variantenID": variantenID},
                    type: "GET",
                    success: function (data) {
                        //  alert(data);
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
        }
    });


</script>
</body>
</html>