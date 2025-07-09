<?php
session_start();
if (!function_exists('utils_connect_sql')) {  include "utils/_utils.php"; }
init_page_serversides();
?> 


<html data-bs-theme="dark" xmlns="http://www.w3.org/1999/xhtml" >
    <head>
        <title>RB-Raumvergleich</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"/>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    </head>        

    <body>

        <div id="limet-navbar" class='bla'> </div>  
        <div class ="container-fluid" id="KONTÄNER">
            <div class="card">
                <div class="card-header"> Header </div>
                <div class="card-body" id="fingerprint">
     
                </div> 
            </div>  
        </div>
        <script src="getBrowserFingerprint.js">
            $(document).ready(function () {
                let fingerprint = getBrowserFingerprint();
                let fingerprintDiv = $('#fingerprint');
                for (let key in fingerprint) {
                    let value = fingerprint[key];
                    if (Array.isArray(value)) {
                        value = value.join(', ');
                    }
                    fingerprintDiv.append(`<p><strong>${key}:</strong> ${value}</p>`);
                }
            });
        </script>  
    </body> 
</html>
 n