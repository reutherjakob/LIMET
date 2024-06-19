<!DOCTYPE html>
<?php
session_start();
include '_utils.php';
init_page_serversides();
?> 

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1">

            <title>Bauangaben check</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
                <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
                    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

                    <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css" rel="stylesheet">
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
                        <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script>

                        <body style="height:100%"> 
                            <div class="container-fluid" >
                                <div id="limet-navbar" class=' '> </div> 

                                <div class="mt-4 card">
                                    <div class="card-header" id="CH1">BAUANGABEN CHECK</div>
                                    <div class="card-body" id="CB_C1"> </div>
                                </div>


                        </body>
                        <script>
                            $(document).ready(function () {
                                $.ajax({
                                    url: "get_angaben_check.php",
                                    type: "GET",
                                    success: function (data) {
                                        const lines = data.split('\n').filter(line => line.trim() !== '');
                                        const roomIssues = {};
                                        lines.forEach(line => {
                                            const parts = line.split(':::');
                                            const ROOM = parts[0].trim();
                                            const kathegorie = parts[1].split('->')[0].trim();
                                            const issue = parts[1].split('->')[1].trim();

                                            roomIssues[ROOM] = roomIssues[ROOM] || [];
                                            roomIssues[ROOM].push({kathegorie, issue}); 
                                        });
                                        const table = document.createElement('table');
                                        table.className = 'table compact';
                                        table.id = 'table1ID';
                                        const thead = document.createElement('thead');
                                        const headerRow = '<tr><th>Room</th><th>Kathegorie</th><th> - </th><th>Problem</th></tr>';
                                        thead.innerHTML = headerRow;
                                        table.appendChild(thead);
                                        const tbody = document.createElement('tbody');
                                        for (const room in roomIssues) {
                                            const rows = roomIssues[room].map(item => {
                                                return `<tr><td>${room}</td><td>${item.kathegorie}</td><td><input type='checkbox'></td><td>${item.issue}</td></tr>`;
                                            });

                                            tbody.innerHTML += rows.join('');
                                        }
                                        table.appendChild(tbody);
                                        document.querySelector('#CB_C1').appendChild(table);
                                    }
                                });
                                new DataTable('#table1ID', {
    dom: 'ti',
    keys: true,
    pageLength: -1,
    compact: true,
    select: 'os' // Set select to 'os'
});

                            });



                        </script> 
                        </html>
