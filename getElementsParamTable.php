 <?php
 // 25 FX
require_once 'utils/_utils.php';
check_login();
$K2Return = $_POST['K2Return'];
$K2Ret = json_decode($K2Return);
?>
 <!DOCTYPE html>
 <html xmlns="http://www.w3.org/1999/xhtml" lang="de">
 <head>
     <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
           integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
     <title>get Elements Parameter Table </title>
 </head>
 <body>
 <div class="card-body " id="elemetsParamsTableCard">
     <table class='table display compact table-striped table-bordered table-sm' id='roomElementsParamTable'>
         <thead>
         <tr></tr>
         </thead>
         <tbody>
         <td></td>
         </tbody>
     </table>
 </div>
 </body>
 </html>

<script>
    var table2;
    $(document).ready(function () {
        make_table();
    });

    function make_table() {
        let K2R = <?php echo json_encode($K2Ret); ?>;

        $.ajax({
            url: 'getRoomElementsParameterData.php',
            method: 'POST',
            dataType: 'json',
            data: {"roomID": <?php echo $_POST["roomID"]; ?>, "K2Return": JSON.stringify(K2R)},
            success: function (data) {
                if (!data || data.length === 0) {
                    // console.log('ajax: getRoomElementsParamTable -> No valid data returned');
                    return;
                }
                let titleMapping = {
                    'Varianate': 'Var',
                    'SummevonAnzahl': '#'
                };
                let columns = Object.keys(data[0]).map(function (key) {
                    let title = titleMapping[key] ? titleMapping[key] : key;
                    return {title: title, data: key};
                });

                let keysToRemove = ['tabelle_Varianten_idtabelle_Varianten', 'TABELLE_Elemente_idTABELLE_Elemente'];
                columns = columns.filter(function (column) {
                    return !keysToRemove.includes(column.data);
                });

                table2 = new DataTable('#roomElementsParamTable', {
                    data: data,
                    columns: columns,
                    dom: 'ti',
                    scrollX: true,
                    paging: false,
                    pageLength: -1
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }
</script>