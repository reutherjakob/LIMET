<?php

// Define log file path
$logFile = __DIR__ . '/sql_debug_log.txt';

function logQuery($sql, $params, $logFile)
{
    $logEntry = "[" . date('Y-m-d H:i:s') . "]\n";
    $logEntry .= "SQL: $sql\n";
    $logEntry .= "Params: " . print_r($params, true);
    $logEntry .= str_repeat("-", 50) . "\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

function logToFile($message)
{
    $logFile = __DIR__ . '/sql_debug.log';  // Log file in the same directory as the script
    $timeStamp = date('Y-m-d H:i:s');
// Append the message with timestamp followed by newline
    file_put_contents($logFile, "[$timeStamp] $message\n", FILE_APPEND);
}

function logFieldValues(array $fields, array $values)
{
    $pairs = [];
    foreach ($fields as $index => $fieldName) {
// Avoid out-of-bounds if arrays differ in length
        $value = $values[$index] ?? '(missing)';
// Escape or sanitize value to safely log (optional)
        $pairs[] = "$fieldName = " . var_export($value, true);
    }
    $logMessage = implode(", ", $pairs);
    logToFile($logMessage);
}

