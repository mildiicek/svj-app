<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="topnav">
    <div class="nav-left">
        <a href="dashboard.php" class="brand">SVJ Aplikace</a>
    </div>

    <div class="nav-links">
        <a href="forum.php">Fórum</a>
        <a href="announcements.php">Oznámení</a>
        <a href="payments.php">Platby</a>
        <a href="reservations.php">Rezervace</a>
        <a href="logout.php">Odhlásit se</a>
    </div>
</nav>