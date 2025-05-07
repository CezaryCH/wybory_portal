<?php
include 'db.php';
$elections = $conn->query("SELECT * FROM elections ORDER BY id DESC");

$current_id = isset($_GET['election_id']) ? intval($_GET['election_id']) : null;

if ($current_id) {
    $stmt = $conn->prepare("SELECT name, votes FROM candidates WHERE election_id = ?");
    $stmt->bind_param("i", $current_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $names = [];
    $votes = [];
    while ($row = $result->fetch_assoc()) {
        $names[] = $row['name'];
        $votes[] = $row['votes'];
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Portal Wyborczy</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h2>Witamy w Portalu Wyborczym</h2>

<div style="margin-bottom: 20px;">
    <a href="register.php"><button>Zarejestruj się</button></a>
    <a href="login.php"><button>Zaloguj się</button></a>
</div>

<form method="GET" style="max-width: 400px; margin: 0 auto;">
    <select name="election_id" onchange="this.form.submit()">
        <option value="">-- Wybierz wybory --</option>
        <?php while ($e = $elections->fetch_assoc()): ?>
            <option value="<?= $e['id'] ?>" <?= ($e['id'] == $current_id ? 'selected' : '') ?>>
                <?= htmlspecialchars($e['name']) ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

<?php if (!empty($names)): ?>
    <canvas id="votesChart"></canvas>
    <script>
        const ctx = document.getElementById('votesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($names) ?>,
                datasets: [{
                    label: 'Głosy',
                    data: <?= json_encode($votes) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            }
        });
    </script>
<?php elseif ($current_id): ?>
    <p>Brak kandydatów dla tych wyborów.</p>
<?php endif; ?>

</body>
</html>
