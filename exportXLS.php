
<?php
session_start();
if (!function_exists('utils_connect_sql')) {  include "utils/_utils.php"; }
check_login(); //print_session_vars();
?> 

<!DOCTYPE html>
<html>
    <head>
        <title>Excel Export</title>
        <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    </head>
    <body>

        <button id="addSheet">Add Sheet</button>
        <button id="download">Download Excel</button>
        <button id="reset">Reset Excel</button>
        <ul id="log"></ul>

        <script>
            var wb = XLSX.utils.book_new();
            var sheetIndex = 1;

            $(document).ready(function () {
                $('#addSheet').click(function () {
                    $.ajax({
                        url: 'getRoomElementsParameterTableData.php',
                        method: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            var ws = XLSX.utils.json_to_sheet(data);
                            XLSX.utils.book_append_sheet(wb, ws, "Sheet" + sheetIndex);
                            $('#log').append('<li>Added Sheet' + sheetIndex++ + '</li>');
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
                    });
                });

                $('#download').click(function () {
                    var wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'binary'});

                    function s2ab(s) {
                        var buf = new ArrayBuffer(s.length);
                        var view = new Uint8Array(buf);
                        for (var i = 0; i < s.length; i++)
                            view[i] = s.charCodeAt(i) & 0xFF;
                        return buf;
                    }

                    saveAs(new Blob([s2ab(wbout)], {type: "application/octet-stream"}), 'data.xlsx');
                });

                $('#reset').click(function () {
                    wb = XLSX.utils.book_new();
                    sheetIndex = 1;
                    $('#log').empty();
                });
            });
        </script>
    </body>
</html>
