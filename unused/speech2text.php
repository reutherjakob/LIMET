<?php 
require_once 'utils/_utils.php';
check_login();
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <title>Speech recognition</title>
        <style>
            #result{
                border: 2px solid black;
                height: 200px;
                border-radius: 3px;
                font-size: 14px;
            }
            button{
                position: absolute;
                top: 240px;
                left: 50%;
            }
        </style>
        <script type="application/javascript">
            function start(){
                var r = document.getElementById("result");
            if("webkitSpeechRecognition" in window){
                var speechRecognizer = new webkitSpeechRecognition();
                speechRecognizer.continuous = true;
                speechRecognizer.interimResults = true;
                speechRecognizer.lang = "de-DE";
                speechRecognizer.start();
                
                var finalTranscripts = "";
                speechRecognizer.onresult = function(event){
                    var interimTranscripts = "";
                    for(var i=event.resultIndex; i<event.results.length; i++){
                        var transcript = event.results[i][0].transcript;
                        transcript.replace("\n", "<br>");
                        if(event.results[i].isFinal){
                            finalTranscripts += transcript + "   ";
                        }
                        else{
                            interimTranscripts += transcript;
                        }
                        r.innerHTML = finalTranscripts + '<span style="color: #999;">' + interimTranscripts + '</span>';
                    }
                };
                speechRecognizer.onerror = function(event){
                };
            }
            else{
                r.innerHTML = "Your browser does not support that.";
            }
            }
        </script>
    </head>
    <body>
        <div id="result"></div>
        <button onclick="start()">Listen</button>
    </body>
</html>


<!--<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Real-time Speech to Text App</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="icon" href="iphone_favicon.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.js"></script>
    <script src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>
</head>-->
<!--<body>
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
    </div>-->
<!--    <script>
        let recognition;
        let recognizedText = ''; // Store continuous transcription

        $(document).ready(function() {
            const toggleBtn = $('#toggleBtn');
            const languageSelect = $('#languageSelect');
            const resultDiv = $('#resultz');

            toggleBtn.on('click', function() {
                if (!recognition) {
                    console.log("Starting Speech Recognition");
                    recognition = new webkitSpeechRecognition();
                    recognition.lang = languageSelect.val(); // Set the language
                    recognition.start();
                    toggleBtn.text('Stop Recognition');
                    resultDiv.text('Listening...');
                    recognition.onresult = function(event) {
                        const alternatives = event.results[0]; // Array of alternatives
                        for (const alt of alternatives) {
                            recognizedText += alt.transcript + ' \n'; // Append to existing transcription
                        }
                        resultDiv.text(recognizedText);
                        if(recognition){
                            recognition.stop();
                            recognition = null;
                            console.log("Stopped");
                            recognition = new webkitSpeechRecognition();
                            recognition.lang = languageSelect.val(); // Set the language
                            recognition.start();
                        }
                    };
                } else {
                    console.log("Stopping Speech Recognition");
                    recognition.stop();
                    toggleBtn.text('Start Recognition');
                    recognition = null;
                }
            });
        });
    </script>-->
<!--</body>
</html>-->
