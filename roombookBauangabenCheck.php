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
                        <style>
                            .checked {
                                text-decoration: line-through;
                            }
                        </style>
                        </head>
                        <body style="height:100%"> 
                            <div class="container-fluid" >
                                <div id="limet-navbar" class=' '> </div> 

                                <div class="mt-4 card responsive">
                                    <div class="card-header" id="CH1">BAUANGABEN CHECK</div> 
                                    <div id="CB_C1"   class="table-responsive"  >
                                        <table class="table display compact table-striped table-bordered table-sm" id="table1ID">
<!--                                            <thead <tr></tr> </thead>
                                            <tbody> <td></td>  </tbody>-->
                                            <thead>
                                                <tr>
                                                    <th> - </th>
                                                    <th>Room</th>

                                                    <th>Kathegorie</th>
                                                    <th>Problem</th>
                                                   
                                                </tr>
                                            </thead>
                                            <tbody id="tableBody"> 
                                            </tbody>
                                        </table>
                                    </div>

                                </div>


                        </body>
                        <script>
                            function getRoomIdsFromCurrentUrl() {
                                var urlObj = new URL(window.location.href);
                                var roomIDParam = urlObj.searchParams.get("roomID");
                                if (roomIDParam) {
                                    var roomIDs = roomIDParam.split(",");
                                    return roomIDs;
                                } else {
                                    return [];
                                }

                            }



                            $(document).ready(function () {

                                ids = getRoomIdsFromCurrentUrl();
                                console.log(ids);
                                $.ajax({
                                    url: "get_angaben_check.php",
                                    type: "GET",
                                    data: {'roomID': ids.join(',')},
                                    success: function (data) {
//                                        console.log(data);
                                        const lines = data.split('\n').filter(line => line.trim() !== '');
                                        const roomIssues = {};
                                        lines.forEach(line => {
                                            const parts = line.split(':::');
                                            const ROOM = parts[0].trim().split('---')[0];
                                            const R_ID = parts[0].trim().split('---')[1];
                                            const kathegorie = parts[1].split('->')[0].trim();
                                            const issue = parts[1].split('->')[1].trim();

                                            roomIssues[R_ID] = roomIssues[R_ID] || [];
                                            roomIssues[R_ID].push({ROOM, kathegorie, issue});
                                        });

                                        const tbody = document.getElementById('tableBody');
                                        for (const id in roomIssues) {
//                                            console.log(id);
                                            const rows = roomIssues[id].map((item, index) => {
                                                const isChecked = localStorage.getItem(`${id}-${index}`) === 'true';
                                                return `<tr class="${isChecked ? 'checked' : ''}" data-id="${id}"> <td><input type='checkbox' ${isChecked ? 'checked' : ''}></td>  <td>${item.ROOM}</td>  <td>${item.kathegorie}</td><td>${item.issue}</td>   </tr>`;
                                            });

                                            tbody.innerHTML += rows.join('');
                                        }

                                        if (tbody.innerHTML.trim() !== '') {

                                            new DataTable('#table1ID', {
                                                dom: ' <"TableCardHeader"f>ti',
                                                language: {
                                                    "search": ""//,
//                                                    searchBuilder: {
//                                                        title: null,
//                                                        depthLimit: 2,
//                                                        stateSave: false
//                                                    }
                                                },
                                                keys: true,
                                                scrollx:true, 
                                                pageLength: -1,
                                                compact: true,
                                                savestate: true,
                                                select: 'os', // Set select to 'os'
                                                columns: [
                                                    {width: '5%'},
                                                    {width: '25%'},
                                                    {width: '20%'},
                                                    {width: '50%'} 
                                                ]
                                            });
                                        } else {
                                            document.getElementById('table1ID').style.display = 'none';
                                        }

                                        $('#table1ID').on('change', 'input[type="checkbox"]', function () {
                                            const tr = $(this).closest('tr');
                                            const id = tr.data('id');
                                            console.log(id);
                                            const issue = tr.find('td:nth-child(4)').text();
                                            const key = `${id}-${issue}`;

                                            if (this.checked) {
                                                tr.addClass('checked');
                                                localStorage.setItem(key, 'true');
                                            } else {
                                                tr.removeClass('checked');
                                                localStorage.setItem(key, 'false');
                                            }
                                        });

                                        // Load the saved data from local storage
                                        $('#table1ID tr').each(function (index, row) {
                                            if (index !== 0) { // Skip the header row
                                                 const id = $(row).data('id'); 
                                                const issue = $(row).find('td:nth-child(4)').text();
                                                const key = `${id}-${issue}`;
                                                const isChecked = localStorage.getItem(key) === 'true';
                                                if (isChecked) {
                                                    $(row).addClass('checked');
                                                    $(row).find('input[type="checkbox"]').prop('checked', true);
                                                }
                                            }
                                        });

                                    }
//                                    else {console.log("No Success");}
                                });
                                setTimeout(function () {
                                    var dt_searcher = document.getElementById("dt-search-0");
                                    dt_searcher.parentNode.removeChild(dt_searcher);
                                    dt_searcher.style.float = "right";
                                    document.getElementById("CH1").appendChild(dt_searcher);
                                }, 200);
                            });


                        </script> 
                        </html>
