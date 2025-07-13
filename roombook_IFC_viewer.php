<?php
session_start();
if (!function_exists('utils_connect_sql')) {  include "utils/_utils.php"; }
check_login(); 
?>  

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>My first three.js app</title>
        <style>
            body {
                margin: 0;
            }
        </style>
        <script type="importmap">
            {
            "imports": {
            "three": "https://cdn.jsdelivr.net/npm/three@v0.165.0/build/three.module.js",
            "three/addons/": "https://cdn.jsdelivr.net/npm/three@v0.165.0/examples/jsm/"
            }
            }
        </script>
    <body>
        <script type="module" src="/three.js"></script>
        hello cube
    </body>
</html>