<?php
include 'db.php';

// Sprawdź, czy election_id jest podane
if (isset($_GET['election_id'])) {
    $election_id = (int)$_GET['election_id'];

    // Pobierz wyniki głosowania dla danego wyboru
    $res = $conn->query("SELECT name, votes FROM candidates WHERE election_id = $election_id");

    $names = [];
    $votes = [];
    while ($row = $res->fetch_assoc()) {
        $names[] = $row['name'];
        $votes[] = $row['votes'];
    }

    // Zwróć dane jako JSON
    echo json_encode(["names" => $names, "votes" => $votes]);
} else {
    echo json_encode(["error" => "Brak wyborów"]);
}
