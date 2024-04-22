<?php

function br2nl($string) {
    $return = str_replace(array("<br/>"), "\n", $string);
  //  $return= str_replace(array("\r\n", "\n\r", "\r", "\n"), "<br/>", $temp);
    return $return;
}


function check_login(){
    if (!isset($_SESSION["username"])) {
        echo "Bitte erst <a href=\"index.php\">einloggen</a>";
        exit;
    }
}

function check_project(){
    if ($_SESSION["projectName"] != "") {
        echo '<script>';
        echo 'var currentP = ' . json_encode( $_SESSION["projectName"]) . ';';
        echo '</script>';
    
    } else {  echo '<script>';
        echo 'var currentP = ' . json_encode( " NIX ") . ';';
        echo '</script>';} 
}

function init_page_serversides() { //and Project. 
    check_login();
    check_project(); 
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

?>

<script>	    
    //Load Navbar onLoad   <div id="limet-navbar"></div> 
    window.onload = function(){
        $.get("navbar.html", function(data){
            $("#limet-navbar").html(data);
            $('.navbar-nav').find('li:nth-child(3)') 
              .addClass('active');
            $('#projectSelected').text("Projekt:"  +currentP);
//            console.log(currentP );
        });
    };    
    
    
    
</script>  