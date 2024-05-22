<?php
session_start();
include '_utils.php';
init_page_serversides();
?> 


<!DOCTYPE html>
<html lang="en">
    <head>

        <title>IFC Model Viewer</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
        <link rel="icon" href="iphone_favicon.png">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <div id="limet-navbar"></div>
            <div class="mt-4 card">
                <div class="card-header form-check-inline" > <b> IFC Viewer </b> </div>
                <div class="card-body" id="IFC_Container" >  </div>
            </div>
        </div>

        <script>
//            const container = document.getElementById('IFC_Container');
//
//            // Handle drag-and-drop events
//            container.addEventListener('dragover', (event) => {
//                event.preventDefault();
//            });
//
//            container.addEventListener('drop', (event) => {
//                event.preventDefault();
//                const file = event.dataTransfer.files[0];
//
//                // Validate if it's an IFC file (you can check the extension or MIME type)
//                if (file.type === 'application/ifc') {
//                    // Load and display the 3D model using Three.js
//                    loadIFCModel(file);
//                } else {
//                    alert('Please drop an IFC file.');
//                }
//            });
//
//            function loadIFCModel(ifcFile) {
//                // Load the IFC model using Three.js (implementation depends on your setup)
//                // For example:
//                const loader = new THREE.IFCLoader();
//                loader.load(ifcFile.name, (model) => {
//                    // Add the model to the scene and render it
//                    // Update the container content with the 3D viewer
//                    container.innerHTML = ''; // Clear any previous content
//                    container.appendChild(model.domElement);
//                });
//            }
//        </script>
    </body>
</html>
