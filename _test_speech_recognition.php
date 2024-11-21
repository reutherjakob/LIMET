<?php
session_start();
include '_utils.php';
check_login();
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <title>Real-time Speech to Text App</title>
    
   <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<link rel="icon" href="iphone_favicon.png">
 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
  
 
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/features/mark.js/datatables.mark.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>
</head>

<body>
    <div class="mt-4 card">
        <div class="card-header"><b>Spracherkennung</b>
            <label class="float-right" id="divSpracherkennungLabel">
                <button id="toggleBtn" class="btn btn-outline-success btn-sm">Start Recognition</button> 
                <select id="languageSelect" class="form-select form-select-sm">
                    <option value="de-DE">German</option>
                    <option value="en-US">English</option>
                    <option value="fr-FR">French</option>
                </select>
                <input type="number" id="delayInput" placeholder="Delay (ms)">
                <button id="delayBtn" class="btn btn-outline-primary btn-sm">Set Delay</button>
            </label> 
        </div>
        <div id="resultz" class="result"></div>
    </div>

    <script>
        let recognition;
        let recognizedText = ''; // Store continuous transcription

        const toggleBtn = document.getElementById('toggleBtn');
        const languageSelect = document.getElementById('languageSelect');
        const resultDiv = document.getElementById('resultz');

        toggleBtn.addEventListener('click', () => {
            if (!recognition) {
                console.log("Starting Speech Recognition");
                recognition = new webkitSpeechRecognition();
                recognition.lang = languageSelect.value; // Set the language
                recognition.start();
                toggleBtn.textContent = 'Stop Recognition';
                resultDiv.textContent = 'Listening...';
                recognition.onresult = (event) => {
                    const alternatives = event.results[0]; // Array of alternatives
                    for (const alt of alternatives) {
                        recognizedText += alt.transcript + ' \n'; // Append to existing transcription
                    }
                    resultDiv.textContent = recognizedText;
                    if(recognition){
                        recognition.stop();
                        recognition = null;
                        console.log("Stoped");
                        recognition = new webkitSpeechRecognition();
                        recognition.lang = languageSelect.value; // Set the language
                        recognition.start();
                    }
                };
            } else {
                console.log("Stopping Speech Recognition");
                recognition.stop();
                toggleBtn.textContent = 'Start Recognition';
                recognition = null;
            }
        });
    </script>
 
</body>


</html>

