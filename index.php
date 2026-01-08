<?php
session_start();

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

require __DIR__ . '/app/autoload.php';
require __DIR__ . '/app/functions.php';

// Get room prices to show in rooms section
$stmt = $database->query("SELECT room_type, price FROM rooms GROUP BY room_type");

$roomPrices = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$year = 2026;
$month = 1;
$monthName = date('F', mktime(0, 0, 0, $month, 1, $year));

// Get available dates for calendars
$availableEconomy = getAvailableDates($database, 'economy', $month, $year);
$availableStandard = getAvailableDates($database, 'standard', $month, $year);
$availableLuxury = getAvailableDates($database, 'luxury', $month, $year);

// Get active features for booking form
$getFeatures = $featuresDatabase->prepare('SELECT * FROM features WHERE active = 1;');
$getFeatures->execute();
$features = $getFeatures->fetchAll(PDO::FETCH_ASSOC);

$featureCategories = [];

foreach ($features as $feature) {
    $category = $feature['category'];
    $featureCategories[$category][] = $feature;
}


require __DIR__ . '/views/header.php';

?>

<main>
    <div class="hero">
        <h1 class="hotelName">Outset Shores<br><span>&#9734</span></h1>
        <img src="assets/images/hotel_image.png" alt="View of Outset island with the hotel in the foreground.">
    </div>
    <section class="mainPage">
        <p class="welcomeMessage"><span class="welcome">Welcome</span> to a secluded tropical retreat where time slows and nature takes the lead. Nestled between lush green hills and the gentle curve of a golden beach, our intimate hotel offers a peaceful escape just steps from the water’s edge. Wake to the sound of the ocean, spend your days beneath swaying palms and turquoise skies, and return to serene, thoughtfully designed rooms as the sun sets over the bay. Here, barefoot comfort meets unspoiled beauty—an invitation to disconnect, breathe deeply, and truly arrive.</p>

        <!-- Section for room types display -->
        <section class="roomTypes">
            <h2 class="roomsHeading">Rooms:</h2>
            <article>
                <div class="calendarContainer">
                    <h3><?= $monthName . " " . $year ?></h3>
                    <?= getCalendar($availableEconomy); ?>
                </div>
                <div class="roomDescription">
                    <h2>Economy $<?= $roomPrices['economy'] ?></h2>
                    <p>Our Economy Rooms offer a warm and inviting retreat for guests who value simplicity and a close connection to nature. Built from natural wood and filled with soft daylight, they provide peaceful views and all the essentials for a comfortable stay—ideal for travelers who spend their days exploring and their evenings unwinding in calm surroundings.
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
                    <h2>Standard $<?= $roomPrices['standard'] ?></h2>
                    <p>The Standard Rooms offer more space and comfort with refined details and a relaxed atmosphere. A seating or work area and doors opening to a private balcony let in natural light and ocean breezes, creating a calm and stylish setting—ideal for longer stays or guests seeking extra comfort.
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
                    <h2>Luxury $<?= $roomPrices['luxury'] ?></h2>
                    <p>The Luxury Rooms offer the highest level of comfort and privacy, with stunning ocean views. Featuring a spacious bed, high ceilings, and a luxurious en suite bathroom, these rooms open onto a private terrace with a plunge pool. Designed for relaxation and indulgence, they provide an exclusive and memorable stay.
                    </p>
                </div>
                <div class="roomTypeImage">
                    <img src="assets/images/luxury_room.png" alt="Luxury room with high ceiling, en suite and private outdoor pool.">
                </div>
            </article>
        </section>

        <!-- Booking form -->
        <h2 class="reservationHeading">Make your reservation for a relaxing holiday here</h2>
        <form class="bookingForm" action="app/booking.php" method="post">
            <div class="formFeatures">
                <label class="featuresHeading" for="features">Additional features:</label>
                <?php foreach ($featureCategories as $category => $f) { ?>
                    <h3><?= htmlspecialchars(ucfirst($category)) ?></h3>

                    <?php foreach ($f as $feature) { ?>
                        <label>
                            <input type="checkbox" id="<?= htmlspecialchars($feature['feature']) ?>" name="features[]" value="<?= $feature['id'] ?>">
                            <?= htmlspecialchars(ucfirst($feature['feature'])) ?> (<?= htmlspecialchars($feature['rank']) ?>, $<?= htmlspecialchars($feature['price']) ?>)
                        </label><br>
                    <?php
                    }
                }
                ?>
            </div>   
            <div class="formInputs">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="e.g. Rune">

                <label for="transferCode">Transfer code:</label>
                <input type="text" id="transferCode" name="transferCode" placeholder="Issued by the central bank">

                <label for="arrival">Arrival:</label>
                <input type="date" id="arrival" name="arrival" min="2026-01-01" max="2026-01-31">

                <label for="departure">Departure:</label>
                <input type="date" id="departure" name="departure" min="2026-01-01" max="2026-01-31">
            
                <label for="roomType">Room type:</label>
                <select id="roomType" name="roomType">
                    <option value="economy">Economy</option>
                    <option value="standard">Standard</option>
                    <option value="luxury">Luxury</option>
                </select>
                <input class="submitButton" type="submit" value="Make reservation">
            </div>
            <div id="formErrors">
                <?php foreach ($errors as $error) { ?>
                    <p class="error"><?= htmlspecialchars($error) ?></p>
                <?php
                } ?>
            </div>
        </form>
    </section>
</main>
<?php
require __DIR__ . '/views/footer.php';