<?php

function br2nl($string) {
    $return = str_replace(array("<br/>"), "\n", $string);
    //  $return= str_replace(array("\r\n", "\n\r", "\r", "\n"), "<br/>", $temp);
    return $return;
}

function check_login() {
    if (!isset($_SESSION["username"])) {
        echo "Bitte erst <a href=\"index.php\">einloggen</a>";
        exit;
    }
}

function check_if_project_selected_else_redirect() {
    if ($_SESSION["projectName"] == "") {
        header("Location: https://work.limet-rb.com/projects.php");
        exit;
    }
}

function get_project() {
    if ($_SESSION["projectName"] != "") {
        echo '<script>';
        echo 'var currentP = ' . json_encode($_SESSION["projectName"]) . ';';
        echo '</script>';
    } else {
        echo '<script>';
        echo 'var currentP = ' . json_encode(" NIX ") . ';';
        echo '</script>';
    }
}

function init_page_serversides($ommit_redirect="") { //and Project. 
    check_login();
    get_project();
    load_nav_bar();
    if($ommit_redirect==""){
        check_if_project_selected_else_redirect(); 
    }
}

function utils_connect_sql() {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    }
    if (!$mysqli->set_charset("utf8")) {
        printf("Error loading character set utf8: %s\n", $mysqli->error);
        exit();
    }
    return $mysqli;
}

function load_nav_bar() {
    echo '<script>';
    echo '    window.onload = function () {';
    echo '        $.get("navbar.html", function (data) {';
    echo '            $("#limet-navbar").html(data);';
    echo '            $(".navbar-nav").find("li:nth-child(3)")';
    echo '                    .addClass("active");';
    echo '            $("#projectSelected").text("Projekt:" + currentP);';
    echo '        });';
    echo '     };    </script>';
} 

function el_in_room_html_table($pdf, $result, $init_einzug) {
    $pdf->MultiCell($init_einzug, 10, "", 0, "C", 0, 0);
    $columnWidthPercentages = array(10, 10, 8, 13, 59);
    $headers = array('ElementID', 'Variante', 'Anzahl', 'Neu/Bestand', 'Bezeichnung'); // 'Standort', 'Verwendung',
    $pdf->SetFont('helvetica', 'B', 12);
    $html = '<table border="0">';
    $html .= '<tr>';
    foreach ($columnWidthPercentages as $index => $widthPercentage) {
        $alignStyle = ($headers[$index] == 'Neu/Bestand' || $headers[$index] == 'Variante' || $headers[$index] == 'Anzahl') ? 'text-align: center;' : ''; // Add this line for centering
        if ($headers[$index] == 'Neu/Bestand') {
            $tablelabel = 'Bestand';
        } else if ($headers[$index] == 'Variante') {
            $tablelabel = 'Var';
        } else {
            $tablelabel = $headers[$index];
        }
        $html .= '<th width="' . $widthPercentage . '%" style="' . $alignStyle . '">' . $tablelabel . '</th>';
    }
    $html .= '</tr>';
    $pdf->SetFont('helvetica', '', 10);

    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>';
        foreach ($columnWidthPercentages as $index => $widthPercentage) {
            $columnName = $headers[$index];
            $cellValue = $row[$columnName] ?? '';

            // Translate 'Neu/Bestand' values
            if ($columnName == 'Neu/Bestand') {
                $cellValue = translateBestand($cellValue);
            }
            $alignStyle = ($columnName == 'Neu/Bestand' || $columnName == 'Variante' || $columnName == 'Anzahl') ? 'text-align: center;' : ''; // Add this line for centering
            $html .= '<td width="' . $widthPercentage . '%" style="' . $alignStyle . '">' . $cellValue . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</table>';

    $pdf->writeHTML($html);
}

?>

