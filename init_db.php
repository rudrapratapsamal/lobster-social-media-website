<?php
// Database configuration
$hostname = "localhost"; // Replace with your database hostname
$username = "root";      // Replace with your database username
$password = "";          // Replace with your database password

// Read the SQL file
$sqlFile = 'lobsterdb.sql'; // Replace with the actual path to your SQL file
$sql = file_get_contents($sqlFile);

// Connect to the MySQL server
$mysqli = new mysqli($hostname, $username, $password);

// Check connection
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit;
}

// Execute the SQL queries to create the database
if ($mysqli->query($sql) === TRUE) {
    echo "Database created successfully.";
} else {
    echo "Error creating database: " . $mysqli->error;
}

// Close the connection
$mysqli->close();
?>
