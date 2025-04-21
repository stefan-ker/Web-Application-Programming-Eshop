<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "my_databaseshop"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης στη βάση δεδομένων: " . $conn->connect_error);
}
?>
