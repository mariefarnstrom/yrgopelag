<?php
session_start();

if (!isset($_SESSION['confirmation'])) {
    echo "No booking data available.";
    exit;
}
$booking = $_SESSION['confirmation'];
unset($_SESSION['confirmation']);

require __DIR__ . '/../views/header.php';
?>
<nav class="confirmationNav"><a href="../index.php">Home</a></nav>
<!-- Show confirmation message with booking details -->
<article class="confirmationMessage">
    <h2>Thank you, <?= htmlspecialchars($booking['guest_name']) ?>! Your reservation has been made.</h2>

    <p><?= ucfirst(htmlspecialchars($booking['room_type'])) ?> room</p>
    <p>Check-in: <?= htmlspecialchars($booking['arrival']) ?> <strong>3 pm</strong></p>
    <p>Check-out: <?= htmlspecialchars($booking['departure']) ?> <strong>11 am</strong></p>
    <?php if(!empty($booking['features'])) { ?>
        <p>Additional features booked:
        <?php echo htmlspecialchars(implode(', ', $booking['features'])); ?>.
        </p>
    <?php } ?>
    <p>Total price: <strong><?= htmlspecialchars($booking['total_price']) ?>$</strong></p>
</article>

<?php
require __DIR__ . '/../views/footer.php';