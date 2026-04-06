<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userRole = $_SESSION["role"] ?? 'user';

// přidání oznámení může jen admin
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($userRole !== 'admin') {
        die("Nemáš oprávnění přidávat oznámení.");
    }

    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);

    if ($title !== "" && $content !== "") {
        $stmt = $db->prepare("INSERT INTO announcements (title, content) VALUES (:title, :content)");
        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
        $stmt->bindValue(':content', $content, SQLITE3_TEXT);
        $stmt->execute();
    }
}

$result = $db->query("SELECT * FROM announcements ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Oznámení</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'menu.php'; ?>
<div class="container">
<h1>Oznámení</h1>

<?php if ($userRole === 'admin'): ?>
    <form method="post">
        <input type="text" name="title" placeholder="Název" required>
        <textarea name="content" placeholder="Text oznámení" required></textarea>
        <button type="submit">Přidat</button>
    </form>
    <hr>
<?php endif; ?>

<?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
    <div>
        <h3><?php echo htmlspecialchars($row["title"]); ?></h3>
        <p><?php echo htmlspecialchars($row["content"]); ?></p>
        <small><?php echo $row["created_at"]; ?></small>
    </div>
    <hr>
<?php endwhile; ?>

<p><a href="dashboard.php">Zpět</a></p>

</div>

</body>
</html>