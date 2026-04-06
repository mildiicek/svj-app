<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $room = trim($_POST["room"]);
    $reservation_date = $_POST["reservation_date"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];
    $note = trim($_POST["note"]);

    if ($room !== "" && $reservation_date !== "" && $start_time !== "" && $end_time !== "") {

        // kontrola překryvu času
        $stmt = $db->prepare("
            SELECT COUNT(*) as count
            FROM reservations
            WHERE room = :room
            AND reservation_date = :reservation_date
            AND NOT (
                end_time <= :start_time OR start_time >= :end_time
            )
        ");

        $stmt->bindValue(':room', $room, SQLITE3_TEXT);
        $stmt->bindValue(':reservation_date', $reservation_date, SQLITE3_TEXT);
        $stmt->bindValue(':start_time', $start_time, SQLITE3_TEXT);
        $stmt->bindValue(':end_time', $end_time, SQLITE3_TEXT);

        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);

        if ($row["count"] > 0) {
            $message = "Tento čas je již rezervovaný.";
        } else {
            $stmt = $db->prepare("
                INSERT INTO reservations (user_id, room, reservation_date, start_time, end_time, note)
                VALUES (:user_id, :room, :reservation_date, :start_time, :end_time, :note)
            ");

            $stmt->bindValue(':user_id', $_SESSION["user_id"], SQLITE3_INTEGER);
            $stmt->bindValue(':room', $room, SQLITE3_TEXT);
            $stmt->bindValue(':reservation_date', $reservation_date, SQLITE3_TEXT);
            $stmt->bindValue(':start_time', $start_time, SQLITE3_TEXT);
            $stmt->bindValue(':end_time', $end_time, SQLITE3_TEXT);
            $stmt->bindValue(':note', $note, SQLITE3_TEXT);

            $stmt->execute();

            $message = "Rezervace vytvořena.";
        }
    } else {
        $message = "Vyplň všechna pole.";
    }
}

// načtení rezervací
$result = $db->query("
    SELECT reservations.*, users.username
    FROM reservations
    JOIN users ON reservations.user_id = users.id
    ORDER BY reservation_date ASC, start_time ASC
");
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Rezervace</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'menu.php'; ?>
<div class="container">
<h1>Rezervace společných prostor</h1>

<form method="post">
    <input type="text" name="room" placeholder="Název prostoru" required>

    <input type="date" name="reservation_date" required>

    <label>Od:</label>
    <input type="time" name="start_time" required>

    <label>Do:</label>
    <input type="time" name="end_time" required>

    <textarea name="note" placeholder="Poznámka"></textarea>

    <button type="submit">Vytvořit rezervaci</button>
</form>

<p><?php echo htmlspecialchars($message); ?></p>

<hr>

<?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
    <div>
        <strong><?php echo htmlspecialchars($row["room"]); ?></strong><br>

        <p>
            <?php echo htmlspecialchars($row["reservation_date"]); ?>
            <?php echo htmlspecialchars($row["start_time"]); ?> -
            <?php echo htmlspecialchars($row["end_time"]); ?>
        </p>

        <p>Rezervoval: <?php echo htmlspecialchars($row["username"]); ?></p>
        <p>Poznámka: <?php echo htmlspecialchars($row["note"]); ?></p>

        <small><?php echo htmlspecialchars($row["created_at"]); ?></small>
    </div>
    <hr>
<?php endwhile; ?>

<p><a href="dashboard.php">Zpět</a></p>

</div>

</body>
</html>