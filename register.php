<?php
session_start();
require 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if ($username !== "" && $password !== "") {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':password', $hashedPassword, SQLITE3_TEXT);

        $result = @$stmt->execute();

        if ($result) {
            $message = "Registrace proběhla úspěšně. <a href='login.php'>Přihlásit se</a>";
        } else {
            $message = "Uživatel už existuje.";
        }
    } else {
        $message = "Vyplň všechna pole.";
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Registrace</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Registrace</h1>
    <form method="post">
        <input type="text" name="username" placeholder="Uživatelské jméno">
        <input type="password" name="password" placeholder="Heslo">
        <button type="submit">Registrovat</button>
    </form>
    <p><?php echo $message; ?></p>
    <p><a href="login.php">Už mám účet</a></p>
</body>
</html>