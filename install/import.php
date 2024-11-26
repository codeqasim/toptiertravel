<?php

include "../_config.php";

$mysqli = new mysqli("localhost", username, password, dbname);

if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

// Disable foreign key checks
$mysqli->query('SET foreign_key_checks = 0');

// Drop all existing tables
if ($result = $mysqli->query("SHOW TABLES")) {
    while ($row = $result->fetch_array(MYSQLI_NUM)) {
        $mysqli->query('DROP TABLE IF EXISTS ' . $row[0]);
    }
    $result->free();
}

// Enable foreign key checks
$mysqli->query('SET foreign_key_checks = 1');

// Load the SQL file
$sqlScript = file('database.sql');
$query = '';

foreach ($sqlScript as $line) {
    $line = trim($line);

    // Skip comments and empty lines
    if (empty($line) || substr($line, 0, 2) === '--' || substr($line, 0, 1) === '#' || substr($line, 0, 2) === '/*') {
        continue;
    }

    // Add this line to the current query
    $query .= $line . ' ';

    // If it ends with a semicolon, execute the query
    if (substr($line, -1) === ';') {
        if (!$mysqli->query($query)) {
            die('<div class="error-response sql-import-response">Problem in executing the SQL query: <b>' . $query . '</b><br>Error: ' . $mysqli->error . '</div>');
        }
        $query = ''; // Reset the query string
    }
}

echo '<div class="success-response sql-import-response">SQL file imported successfully</div>';

$mysqli->close();

?>
