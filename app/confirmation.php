<?php
session_start();

if (!isset($_SESSION['confirmation'])) {
    echo "No booking data available.";
    exit;
}
$booking = $_SESSION['confirmation'];
// unset($_SESSION['confirmation']);

require __DIR__ . '/../views/header.php';
?>
<nav class="confirmationNav"><a href="../index.php">Home</a></nav>
<article class="confirmationMessage">
    <h2>Thank you, <?= $booking['guest_name'] ?>! Your reservation has been made.</h2>

    <p><?= ucfirst($booking['room_type']) ?> room</p>
    <p>Check-in: <?= $booking['arrival'] ?> <strong>3 pm</strong></p>
    <p>Check-out: <?= $booking['departure'] ?> <strong>11 am</strong></p>
    <p>Additional features booked: <?php foreach ($booking['features'] as $feature) {
        echo " " . $feature;
    } ?>.
    </p>
    <p>Total price: <?= $booking['total_price'] ?>$</p>
</article>

<?php
require __DIR__ . '/../views/footer.php';