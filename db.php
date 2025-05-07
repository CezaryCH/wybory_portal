<?php
$servername = "localhost";
$username = "root";        // lub inny użytkownik bazy
$password = "";            // hasło do bazy
$dbname = "wybory_portal";        // nazwa Twojej bazy danych

$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}
?>
