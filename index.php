<?php
session_start();

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

require __DIR__ . '/app/autoload.php';
require __DIR__ . '/app/functions.php';

$year = 2026;
$month = 1;
$monthName = date('F', mktime(0, 0, 0, $month, 1, $year));

// Get available dates for calendars
$availableEconomy = getAvailableDates($database, 'economy', $month, $year);
$availableStandard = getAvailableDates($database, 'standard', $month, $year);
$availableLuxury = getAvailableDates($database, 'luxury', $month, $year);

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
    <div class="hero">
        <h1 class="hotelName">Outset Shores</h1>
        <img src="assets/images/hotel_image.png" alt="View of Outset island with the hotel in the foreground.">
    </div>
    <section>
        <h2>Welcome</h2>
        <p>The Outset Shores Hotel kkdn kjdnvjkn kdjnvk kjnv skjn skkjvd lkdn sdkbf kjndf kjsndf kjnd lkmf lfn lrnfkjbf lsnkb</p>
    </section>
    <!-- Section for room types display -->
    <section class="roomTypes">
        <article>
            <div class="calendarContainer">
                <h3><?= $monthName . " " . $year ?></h3>
                <?= getCalendar($availableEconomy); ?>
            </div>
            <div class="roomDescription">
                <h2>Economy</h2>
                <p>Bla bla dlkvmdfkvdm dfknd lkmfv lkfv lknfv klnf jdom pogkb podf k ok spokrn lrkfmn ldv lfr ofkv lofmv lf kfv dfvdb
                </p>
            </div>
            <div class="roomTypeImage">
                <img src="assets/images/economy_room.png" alt="Small economy room with a double bed and an ocean view.">
            </div>
        </article>
        <article>
            <div class="calendarContainer">
                <h3><?= $monthName . " " . $year ?></h3>
                <?= getCalendar($availableStandard); ?>
            </div>
            <div class="roomDescription">
                <h2>Standard</h2>
                <p>Bla bla dlkvmdfkvdm dfknd lkmfv lkfv lknfv klnf jdom pogkb podf k ok spokrn lrkfmn ldv lfr ofkv lofmv lf kfv dfvdb
                </p>
            </div>
            <div class="roomTypeImage">
                <img src="assets/images/standard_room.png" alt="Standard room with a double bed and an ocean view.">
            </div>
        </article>
        <article>
            <div class="calendarContainer">
                <h3><?= $monthName . " " . $year ?></h3>
                <?= getCalendar($availableLuxury); ?>
            </div>
            <div class="roomDescription">
                <h2>Luxury</h2>
                <p>Bla bla dlkvmdfkvdm dfknd lkmfv lkfv lknfv klnf jdom pogkb podf k ok spokrn lrkfmn ldv lfr ofkv lofmv lf kfv dfvdb
                </p>
            </div>
            <div class="roomTypeImage">
                <img src="assets/images/luxury_room.png" alt="Luxury room with high ceiling, en suite and private outdoor pool.">
            </div>
        </article>
    </section>

    <!-- Booking form -->
    <form class="bookingForm" action="app/booking.php" method="post">
        <label for="name">Name</label>
        <input type="text" id="name" name="name">

        <label for="transferCode">Transfer code</label>
        <input type="text" id="transferCode" name="transferCode">

        <label for="arrival">Arrival</label>
        <input type="date" id="arrival" name="arrival" min="2026-01-01" max="2026-01-31">

        <label for="departure">Departure</label>
        <input type="date" id="departure" name="departure" min="2026-01-01" max="2026-01-31">
       
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