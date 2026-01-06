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
        <p class="welcomeMessage"><span class="welcome">Welcome</span> to a secluded tropical retreat where time slows and nature takes the lead. Nestled between lush green hills and the gentle curve of a golden beach, our handcrafted wooden villas offer an intimate escape right at the water’s edge. Wake to the sound of the ocean, spend your days beneath swaying palms and turquoise skies, and unwind in serene comfort as the sun sets over the bay. Here, barefoot luxury meets unspoiled beauty—an invitation to disconnect, breathe deeply, and truly arrive.</p>

        <!-- Section for room types display -->
        <section class="roomTypes">
            <h2 class="roomsHeading">Rooms:</h2>
            <article>
                <div class="calendarContainer">
                    <h3><?= $monthName . " " . $year ?></h3>
                    <?= getCalendar($availableEconomy); ?>
                </div>
                <div class="roomDescription">
                    <h2>Economy</h2>
                    <p>Our Economy Rooms offer a warm, inviting retreat designed for travelers who value simplicity and connection to nature. Crafted entirely from natural wood, these rooms are filled with soft daylight and open onto peaceful views of palm trees, the beach, or the sea beyond. Thoughtfully furnished with handcrafted pieces and calming neutral tones, they provide everything you need for a restful stay—perfect for guests who plan to spend their days exploring the shore and their nights unwinding in quiet comfort.
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
                    <p>The Standard Rooms elevate your stay with more space, refined details, and a tranquil indoor-outdoor flow. Featuring a comfortable seating or work area and large doors that open onto a private balcony, these rooms invite the tropical landscape inside. Gentle ocean breezes, warm wood textures, and soft lighting create an atmosphere that feels both relaxed and elegant—ideal for longer stays or guests seeking a balance of comfort and style.
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
                    <p>Our Luxury Rooms are the most exclusive expression of the villa, designed for guests seeking privacy, romance, and breathtaking views. Centered around a spacious four-poster bed, these rooms open directly onto a private terrace with uninterrupted ocean scenery and an intimate plunge pool. High ceilings, handcrafted furnishings, and carefully curated details create a sense of effortless indulgence. Whether watching the sunrise over the water or enjoying a quiet evening indoors, the Luxury Room offers a truly unforgettable escape.
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
                <?php foreach ($errors as $error): ?>
                    <p class="error"><?= htmlspecialchars($error) ?></p>
                <?php endforeach;
                ?>
            </div>
        </form>
    </section>
</main>
<?php
require __DIR__ . '/views/footer.php';