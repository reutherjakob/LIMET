<!DOCTYPE html>
<?php
session_start();
include '_utils.php';
init_page_serversides();
?> 

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="UTF-8">
            <title>Bauangaben check</title>
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
                <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> 
                    </head>
                    
    <body style="height:100%"> 
                        <div class="container-fluid" >
                            <div id="limet-navbar" class=' '> </div> 

                            <div class="mt-4 card">
                                <div class="card-header form-check-inline form-check-inline justify-content align-items-start" id ="CH1"> BAUANGABEN CHECK </div>                                      </div> 
                                <div class="card-body" id = "CB_C2"></div>
                              
                            </div>
                    </body>
                    <script>
                        $(document).ready(function () { 
                            $.ajax({
                                url: "get_angaben_check.php", // "ID": raumID,

                                type: "GET",
                                success: function (data) {

                                    const lines = (data).split('\n').filter(line => line.trim() !== '');
                                    const sections = lines.split(":::");
//                                    let tableHtml = "<table>";
//tableHtml += "<tr><th>Raum</th><th>Check</th><th>Section 3</th> <th>0</th></tr>";
//for (let i = 0; i < sections.length; i += 2) {
//    const section1 = sections[i].trim();
//    const section2 = sections[i + 1].trim();
//
//    // Add a row for each section
//    tableHtml += `<tr><td>${section1}</td><td>${section2}</td> <td><input type="checkbox"></td></tr>`;
//}tableHtml += "</table>";
//document.querySelector('#CB_C2').innerHTML = tableHtml;
                                    const ul = document.createElement('ul');
                                    ul.id = 'myChecklist'; 
                                    
                                    lines.forEach((line) => {
                                        const li = document.createElement('li');
                                        li.textContent = line;
                                        ul.appendChild(li);
                                    });

                                    // Append the unordered list to the card-body element
                                    const cardBody = document.querySelector('#CB_C2');
                                    cardBody.appendChild(ul);
                                }
                            });
                        });

                        function translateBrToNewline(inputString) {
                            const outputString = inputString.replace(/<br>/g, '\n').replace(/<\/br>/g, '\n');
                            return outputString;
                        }

                    </script> 
                    </html>
