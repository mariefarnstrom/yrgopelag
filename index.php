<?php
session_start();

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

require __DIR__ . '/app/autoload.php';

// Get active features for booking form
$getFeatures = $database->prepare('SELECT * FROM features WHERE active = 1;');
$getFeatures->execute();
$features = $getFeatures->fetchAll(PDO::FETCH_ASSOC);


require __DIR__ . '/views/header.php';

foreach ($errors as $error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endforeach;
?>

<main>
    <form class="bookingForm" action="app/booking.php" method="post">
        <label for="name">Name</label>
        <input type="text" id="name" name="name">

        <label for="transferCode">Transfer code</label>
        <input type="text" id="transferCode" name="transferCode">

        <label for="arrival">Arrival</label>
        <input type="date" id="arrival" name="arrival">

        <label for="departure">Departure</label>
        <input type="date" id="departure" name="departure">
       
        <label for="roomType">Room type</label>
        <select id="roomType" name="roomType">
            <option value="economy">Economy</option>
            <option value="standard">Standard</option>
            <option value="luxury">Luxury</option>
        </select>

        <label for="features"></label>
        <?php foreach ($features as $feature) {
            ?><label><input type="checkbox" id="<?=$feature['feature']?>" name="features[]" value="<?=$feature['id']?>"><?= $feature['feature'] . " (" . $feature['rank'] . ", $" . $feature['price'] . ")" ?></label>

            <?php
        }
        ?>
        <input type="submit" value="Submit">
        
    </form>
    
</main>
<?php
require __DIR__ . '/views/footer.php';