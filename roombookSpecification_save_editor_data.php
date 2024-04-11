<?php
$db_host = 'localhost';
$db_user = $_SESSION["username"];
$db_pass = $_SESSION["password"];
$db_name = 'LIMET_RB';
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


// Handle DataTables Editor data// Assuming data is sent via POST
$data = $_POST['data']; // Assuming data is sent via POST

foreach ($data as $row) {
    $RID = mysqli_real_escape_string($conn, $row['Raum ID']);
    $MTrel = mysqli_real_escape_string($conn, $row['MT-relevant']);
    $Raumbezeichnung = mysqli_real_escape_string($conn, $row['Raumbezeichnung']);
    $Raumnr = mysqli_real_escape_string($conn, $row['Raumnrition']);
    $RaumbereichNutzer = mysqli_real_escape_string($conn, $row['Raumbereich Nutzer']);
    $H6020 = mysqli_real_escape_string($conn, $row['H6020']); 

    // Check if the record already exists (based on some unique identifier, e.g., employee ID)
    $sql = "SELECT id FROM employees WHERE first_name = '$RID' AND last_name = '$Raumbezeichnung'";
    
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Update existing record
        $row = mysqli_fetch_assoc($result);
        $id = $row['id'];
        $update_sql = "UPDATE employees SET 'Raumnr' = '$Raumnr', office = '$RaumbereichNutzer', extn = '$H6020',
                       start_date = '$start_date', salary = $salary WHERE `Raum ID´ = $RID";
        mysqli_query($conn, $update_sql);
    } else {
        // Insert new record
        $insert_sql = "INSERT INTO employees (first_name, last_name, position, office, extn, start_date, salary)
                       VALUES ('$MTrel', '$Raumbezeichnung', '$Raumnr', '$RaumbereichNutzer', '$H6020', '$start_date', $salary)";
        mysqli_query($conn, $insert_sql);
    }
}

mysqli_close($conn);
echo json_encode(['status' => 'success', 'message' => 'Data saved successfully']);
?>