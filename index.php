<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['API_KEY'] ?? null;

require __DIR__ . '/views/header.php';
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
        <input type="submit" value="Submit">
        
    </form>
    
</main>
<?php
require __DIR__ . '/views/footer.php';