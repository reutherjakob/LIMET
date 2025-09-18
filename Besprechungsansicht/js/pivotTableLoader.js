// pivotTableLoader.js

function loadPivotTable(params = {}) {
    let raumbereich = $('#raumbereich').val() || [];
    if (raumbereich.length === 0) {
        $('#pivotTableContainer').html('<div class="alert alert-info">Bitte wählen Sie mindestens einen Raumbereich.</div>');
        return;
    }
    let data = {
        action: "loadTable",
        'raumbereich[]': raumbereich,
        'zusatzRaeume[]': $('#zusatzRaeume').val() || [],
        'zusatzElemente[]': $('#zusatzElemente').val() || [],
        mtRelevant: $('#mtRelevant').is(':checked') ? 1 : 0,
        entfallen: $('#entfallen').is(':checked') ? 1 : 0,
        nurMitElementen: $('#nurMitElementen').is(':checked') ? 1 : 0,
        ohneLeereElemente: $('#ohneLeereElemente').is(':checked') ? 1 : 0,
        transponiert: $('#isTransposed').is(':checked') ? 1 : 0,
        ...params // allow override or extra params
    };

    let hideZeros = $('#hideZeros').is(':checked');

    $.ajax({
        url: '../controllers/PivotTableController.php',
        method: 'POST',
        data: data,
        traditional: true,
        success: function (html) {
            console.log("Success loading Table");
            $('#pivotTableContainer').html(html);
            let colCount = $('#pivotTable thead th').length;
            let columns = [];
            for (let i = 0; i < colCount; i++) {
                if (i === 0) columns.push(null);
                else if (hideZeros) {
                    columns.push({
                        render: function (data) {
                            return (data === "0" || data === 0) ? "" : data;
                        }
                    });
                } else {
                    columns.push(null);
                }
            }

            $('#pivotTable').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                    searchPlaceholder: "Suche...",
                    lengthMenu: "_MENU_" , // show only the select dropdown without 'Zeilen anzeigen'
                    info: "von _MAX_ Einträgen",
                    infoEmpty: "Keine Daten vorhanden",
                    infoFiltered: ""  // hides the "(gefiltert von ...)" text
                },
                scrollX: true,
                fixedColumns: {start: 1},
                fixedHeader: true,
                paging: true,
                pagingType: "numbers",
                searching: true,
                ordering: true,
                info: true,
                lengthChange: true,
                pageLength: 10,
                lengthMenu: [[10, 20, 50, -1], ['10 rows', '20 rows', '50 rows', 'All']],
                responsive: false,
                autoWidth: true,
                columns: columns,
                layout: {
                    topStart: 'buttons',
                    topEnd: 'search',
                    bottomStart: 'info',
                    bottomEnd: ['pageLength', 'paging']
                },
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        title: "PivotTableExport"
                    }
                ],
                initComplete: function () {
                    $('#CardHeaderHoldingDatatableManipulators').empty();
                    $('#CardHeaderHoldingDatatableManipulators2').empty();
                    $('#pivotTable_wrapper .dt-buttons').appendTo('#CardHeaderHoldingDatatableManipulators');
                    $('#pivotTable_wrapper .dt-search').appendTo('#CardHeaderHoldingDatatableManipulators');
                    $('#pivotTable_wrapper .dt-length').appendTo('#CardHeaderHoldingDatatableManipulators2');
                    $('#pivotTable_wrapper .dt-info').addClass("btn btn-sm").appendTo('#CardHeaderHoldingDatatableManipulators2');
                    $('#pivotTable_wrapper .dt-paging').addClass("btn btn-sm").appendTo('#CardHeaderHoldingDatatableManipulators2');
                    $('.dt-search label').remove();
                    $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark");


                    //For debugging
                   // $('#pivotTable').off('click', 'td').on('click', 'td', function () {
                   //     const cell = $(this);
                   //     const table = $('#pivotTable').DataTable();            // DataTable cell/row/col index
                   //     const cellIdx = table.cell(this).index();            // Row and column indices (zero-based)
                   //     const rowIdx = cellIdx.row;
                   //     const colIdx = cellIdx.column;                          // Get raw data for this row and column
                   //     const cellData = table.cell(cell).data();
                   //     const rowData = table.row(rowIdx).data();            // Get the header text for this column
                   //     const headerText = $(table.column(colIdx).header()).text().trim();
                   //     const dataRoomId = cell.data('room-id');
                   //     const dataElementId = cell.data('element-id');
                   //     const idTABELLE_Räume_has_tabelle_Elemente = cell.data('relation-id')
                   //     console.log(' --- PIVOT CLICK --- \n Cell Value:', cellData, 'Column:', colIdx, '(', headerText, ')', 'Row:', rowIdx, rowData);
                   //     if (dataRoomId && dataElementId && idTABELLE_Räume_has_tabelle_Elemente) {
                   //         console.log('Room ID:', dataRoomId, 'Element ID:', dataElementId, 'relation ID:', idTABELLE_Räume_has_tabelle_Elemente);
                   //     }
                   // });
                }
            });
        },
        error: function () {
            $('#pivotTableContainer').html('<div class="alert alert-danger">Fehler beim Laden der Tabelle</div>');
        }
    });
}
