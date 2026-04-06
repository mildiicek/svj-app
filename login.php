<?php
session_start();
require 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute();

    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
	$_SESSION["role"] = $user["role"];
        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Neplatné přihlašovací údaje.";
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Přihlášení</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Přihlášení</h1>
    <form method="post">
        <input type="text" name="username" placeholder="Uživatelské jméno">
        <input type="password" name="password" placeholder="Heslo">
        <button type="submit">Přihlásit se</button>
    </form>
    <p><?php echo $message; ?></p>
    <p><a href="register.php">Nemám účet</a></p>
</body>
</html>