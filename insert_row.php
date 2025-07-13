<?php
if (!function_exists('utils_connect_sql')) {  include "utils/_utils.php"; }
$mysqli = utils_connect_sql();

// Check if the form was submitted via AJAX
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['table_name'], $_POST['values'])) {
    $table_name = $_POST['table_name'];
    $values = $_POST['values'];

    // Ensure the table name and values are not empty
    if (!empty($table_name) && !empty($values)) {
        // Filter out empty values
        $filtered_values = array_filter($values, function($value) {
            return trim($value) !== ''; // Only keep non-empty values
        });

        if (!empty($filtered_values)) {
            // Build the dynamic SQL query for insertion
            $columns = implode(", ", array_keys($filtered_values));
            $placeholders = implode(", ", array_fill(0, count($filtered_values), '?'));

            // Prepare the query
            $stmt = $mysqli->prepare("INSERT INTO $table_name ($columns) VALUES ($placeholders)");

            // Dynamically bind parameters
            $types = '';
            $bind_values = [];

            foreach ($filtered_values as $key => $value) {
                $bind_values[] = &$filtered_values[$key]; // Use reference for bind_param
                if (is_int($value)) {
                    $types .= 'i';  // Integer
                } elseif (is_float($value)) {
                    $types .= 'd';  // Double/Float
                } else {
                    $types .= 's';  // String
                }
            }

            // Bind parameters
            array_unshift($bind_values, $types); // Add types as first argument
            call_user_func_array([$stmt, 'bind_param'], $bind_values);

            // Execute the query and send feedback
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'New record inserted successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'All values are empty or invalid.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Table name or values are missing.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$mysqli->close();
?>
