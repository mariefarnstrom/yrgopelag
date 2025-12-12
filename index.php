<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['API_KEY'] ?? null;

require __DIR__ . '/views/header.php';
?>

<main>
    <form class="bookingForm" action="" method="post">
        <label for="">Name</label>
        <input type="text">

        <label for="">Transfer code</label>
        <input type="text">

        <label for="">Arrival</label>
        <input type="date">

        <label for="">Departure</label>
        <input type="date">
       
        <label>Room type</label>
        <select>
            <option value="economy">Economy</option>
            <option value="standard">Standard</option>
            <option value="luxury">Luxury</option>
        </select>
        <input type="submit" value="Submit">
        



    </form>
    <label for=""></label>
</main>
<?php
require __DIR__ . '/views/footer.php';