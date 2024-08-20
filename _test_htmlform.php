<?php
session_start();
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Device Parameters</title>
</head>
<body>
    <h1>Enter Device Parameters for Device 4</h1>
    <form action="_generate_pdf.php" method="post">
        <label for="device4-length">Geometry - Length (mm):</label>
        <input type="number" id="device4-length" name="device4[length]"><br>
        <label for="device4-width">Geometry - Width (mm):</label>
        <input type="number" id="device4-width" name="device4[width]"><br>
        <label for="device4-height">Geometry - Height (mm):</label>
        <input type="number" id="device4-height" name="device4[height]"><br>
        <label for="device4-voltage">Electrical - Voltage (V):</label>
        <input type="number" id="device4-voltage" name="device4[voltage]"><br>
        <label for="device4-current">Electrical - Current (A):</label>
        <input type="number" id="device4-current" name="device4[current]"><br>
        <label for="device4-heating">HKLS - Heating Power (W):</label>
        <input type="number" id="device4-heating" name="device4[heating]"><br>
        <label for="device4-cooling">HKLS - Cooling Power (W):</label>
        <input type="number" id="device4-cooling" name="device4[cooling]"><br>
        <label for="device4-lighting">HKLS - Lig hting (lm):</label>
        <input type="number" id="device4-lighting" name="device4[lighting]"><br> 
        <input type="submit" value="Generate PDF">
    </form>
</body>
</html>
  