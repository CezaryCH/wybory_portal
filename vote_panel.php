<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];
$now = date("Y-m-d H:i:s");

$sql = "SELECT * FROM elections WHERE start_time <= '$now' AND end_time >= '$now'";
$elections = $conn->query($sql);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $election_id = $_POST['election_id'];

    // Sprawdzenie, czy token już istnieje
    $check = $conn->prepare("SELECT * FROM vote_tokens WHERE user_id = ? AND election_id = ?");
    $check->bind_param("ii", $user_id, $election_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo "Już wygenerowano token dla tych wyborów.";
    } else {
        // Generowanie tokenu
        $token = bin2hex(random_bytes(16));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));
        $stmt = $conn->prepare("INSERT INTO vote_tokens (user_id, election_id, token, expires_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $user_id, $election_id, $token, $expires);
        $stmt->execute();
        echo "Twój link do głosowania: <a href='vote.php?token=$token'>Głosuj</a>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel Głosowania</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f8f9fa;
        }
        h2 {
            margin-bottom: 20px;
            color: #343a40;
        }
        .links a {
            margin-right: 15px;
            text-decoration: none;
            background: #007BFF;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .vote-form {
            margin-bottom: 20px;
        }
        .vote-form select {
            padding: 10px;
            font-size: 16px;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .vote-form button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .vote-form button:hover {
            background-color: #0056b3;
        }
        .vote-info {
            margin-top: 20px;
            font-size: 16px;
        }
        .vote-info p {
            margin: 10px 0;
        }
    </style>
</head>
<body>

<h2>Panel Głosowania</h2>

<div class="links">
    <a href="dashboard.php">Powrót do panelu głównego</a>
    <a href="change_password.php">Zmień hasło</a>
    <a href="logout.php">Wyloguj</a>
</div>

<div class="container">
    <h3>Wybierz wybory:</h3>
    <form method="POST" class="vote-form">
        <select name="election_id" required>
            <option value="">-- wybierz wybory --</option>
            <?php while ($row = $elections->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Generuj token do głosowania</button>
    </form>

    <?php
    // Wyświetlanie komunikatu po wygenerowaniu tokenu
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        echo "<div class='vote-info'>";
        echo "Twój link do głosowania: <a href='vote.php?token=$token'>Głosuj</a>";
        echo "</div>";
    }
    ?>
</div>

</body>
</html>
