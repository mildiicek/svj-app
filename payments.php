<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $amount = $_POST["amount"];
    $description = trim($_POST["description"]);

    if ($amount !== "" && $description !== "") {
        $stmt = $db->prepare("INSERT INTO payments (user_id, amount, description) VALUES (:user_id, :amount, :description)");
        $stmt->bindValue(':user_id', $_SESSION["user_id"], SQLITE3_INTEGER);
        $stmt->bindValue(':amount', $amount, SQLITE3_FLOAT);
        $stmt->bindValue(':description', $description, SQLITE3_TEXT);
        $stmt->execute();
    }
}

$result = $db->query("
    SELECT payments.*, users.username 
    FROM payments 
    JOIN users ON payments.user_id = users.id 
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Platby</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'menu.php'; ?>
<div class="container">
<h1>Platby</h1>

<form method="post">
    <input type="number" step="0.01" name="amount" placeholder="Částka" required>
    <input type="text" name="description" placeholder="Popis" required>
    <button type="submit">Přidat platbu</button>
</form>

<hr>

<?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
    <div>
        <strong><?php echo htmlspecialchars($row["username"]); ?></strong><br>
        <p><?php echo htmlspecialchars($row["description"]); ?></p>
        <p><b><?php echo $row["amount"]; ?> Kč</b></p>
        <small><?php echo $row["created_at"]; ?></small>
    </div>
    <hr>
<?php endwhile; ?>

<p><a href="dashboard.php">Zpět</a></p>

</div>

</body>
</html>