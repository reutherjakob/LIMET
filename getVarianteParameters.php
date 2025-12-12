<?php
// 25 FX
include "utils/_utils.php";
include "getElementParameterTable.php";
check_login();
generate_variante_parameter_inputtable();
?>

<script src="utils/_utils.js"></script>
<script src="saveElementParameters.js"></script>
<script>
    $(document).ready(function () {
        document.querySelectorAll('select[id^="Wert_"], select[id^="Einheit_"]').forEach(function (select) {
            select.addEventListener('change', function () {
                const freetextInput = document.getElementById(this.id + '_freetext');
                if (this.value === '__freetext__') {
                    freetextInput.style.display = 'block';
                } else {
                    freetextInput.style.display = 'none';
                    freetextInput.value = '';
                }
            });
        });

        $('#tableElementParameters').DataTable({ //same as in getElementVariante.php
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
                $('#variantenParameterCH .xxx').remove();
                $('#variantenParameter .dt-search label').remove();
                $('#variantenParameter .dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark xxx").prependTo('#variantenParameterCH');
            }
        });
    });
</script>
