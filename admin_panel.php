<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}
include 'db.php';

// Dodawanie nowych wyborów
if (isset($_POST['create_election'])) {
    $name = $_POST['election_name'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];

    $stmt = $conn->prepare("INSERT INTO elections (name, start_time, end_time) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $start, $end);
    $stmt->execute();
}

// Dodawanie nowego kandydata
if (isset($_POST['add_candidate'])) {
    $election_id = $_POST['election_id'];
    $name = $_POST['candidate_name'];
    $description = $_POST['candidate_description'];

    $stmt = $conn->prepare("INSERT INTO candidates (name, description, election_id, votes) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("ssi", $name, $description, $election_id);
    $stmt->execute();
}

$elections = $conn->query("SELECT * FROM elections ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Admina</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f8f9fa;
        }

        h2, h3 {
            color: #343a40;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        form {
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="datetime-local"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        textarea {
            resize: vertical;
            height: 80px;
        }

        button {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #007BFF;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .election-item {
            margin-bottom: 10px;
            padding: 10px;
            background: #f1f1f1;
            border-radius: 5px;
        }

        .logout-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007BFF;
            text-decoration: none;
        }

        .logout-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Panel Admina</h2>
    <a href="logout.php" class="logout-link">Wyloguj się</a>

    <h3>Dodaj nowe wybory</h3>
    <form method="POST">
        <label>Nazwa wyborów:</label>
        <input type="text" name="election_name" required>

        <label>Data rozpoczęcia:</label>
        <input type="datetime-local" name="start_time" required>

        <label>Data zakończenia:</label>
        <input type="datetime-local" name="end_time" required>

        <button type="submit" name="create_election">Utwórz wybory</button>
    </form>

    <h3>Dodaj kandydata</h3>
    <form method="POST">
        <label>Wybory:</label>
        <select name="election_id" required>
            <option value="">-- wybierz wybory --</option>
            <?php
            $res = $conn->query("SELECT * FROM elections ORDER BY id DESC");
            while ($row = $res->fetch_assoc()) {
                echo "<option value='{$row['id']}'>" . htmlspecialchars($row['name']) . "</option>";
            }
            ?>
        </select>

        <label>Imię i nazwisko kandydata:</label>
        <input type="text" name="candidate_name" required>

        <label>Opis kandydata:</label>
        <textarea name="candidate_description" required></textarea>

        <button type="submit" name="add_candidate">Dodaj kandydata</button>
    </form>

    <h3>Lista wyborów</h3>
    <?php while ($row = $elections->fetch_assoc()): ?>
        <div class="election-item">
            <strong><?= htmlspecialchars($row['name']) ?></strong><br>
            <?= $row['start_time'] ?> - <?= $row['end_time'] ?>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>

