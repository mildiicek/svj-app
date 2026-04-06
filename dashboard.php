<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'menu.php'; ?>

<div class="container">
    <h1>Vítej, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
    <p>Toto je hlavní stránka aplikace SVJ.</p>

    <div class="card">
        <h2>Hlavní menu</h2>
        <p>Pomocí horní navigace můžeš přejít do fóra, oznámení, plateb a rezervací.</p>
    </div>
</div>

</body>
</html>