<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// přidání příspěvku
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $content = trim($_POST["content"]);

    if ($content !== "") {
        $stmt = $db->prepare("INSERT INTO posts (user_id, content) VALUES (:user_id, :content)");
        $stmt->bindValue(':user_id', $_SESSION["user_id"], SQLITE3_INTEGER);
        $stmt->bindValue(':content', $content, SQLITE3_TEXT);
        $stmt->execute();
    }
}

// načtení příspěvků
$result = $db->query("
    SELECT posts.*, users.username 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Fórum</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'menu.php'; ?>

<div class="container">

<h1>Fórum</h1>

<form method="post">
    <textarea name="content" placeholder="Napiš příspěvek..." required></textarea>
    <button type="submit">Odeslat</button>
</form>

<hr>

<?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
    <div class="card">
        <strong><?php echo htmlspecialchars($row["username"]); ?></strong><br>
        <p><?php echo htmlspecialchars($row["content"]); ?></p>
        <small><?php echo htmlspecialchars($row["created_at"]); ?></small>
    </div>
<?php endwhile; ?>

</div>

</body>
</html>