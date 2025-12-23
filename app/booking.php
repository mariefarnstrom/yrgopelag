<?php
declare(strict_types=1);

require __DIR__ . '/autoload.php';

// $database = new PDO('sqlite:' . __DIR__ . '/../hotel.db');
$errors = [];

if(isset($_POST['name'], $_POST['transferCode'], $_POST['arrival'], $_POST['departure'], $_POST['roomType'])) {
    $name = trim($_POST['name']);
    $transferCode = trim($_POST['transferCode']);
    $arrival = $_POST['arrival'];
    $departure = $_POST['departure'];
    $roomType = $_POST['roomType'];

    // Get array of chosen features
    $selectedFeatures = $_POST['features'] ?? [];
    $features = [];

    foreach ($selectedFeatures as $selected) {
        $chosenFeature = $database->prepare('SELECT price FROM features WHERE id = :feature');
        $chosenFeature->bindParam(':feature', $selected, PDO::PARAM_STR);
        $chosenFeature->execute();
        $selectedFeature = $chosenFeature->fetch(PDO::FETCH_ASSOC);
        $features[] = $selectedFeature;
    }

    // Calculate price for chosen features
    $featuresTotal = 0;

    foreach ($features as $feature) {
        $featuresTotal += $feature['price'];
    }

    // Look for invalid inputs
    if($name === "") {
        $errors[] = "Please enter your name!";
    }

    if($transferCode === "") {
        $errors[] = "Please enter a valid transfer code!";
    }

    if($arrival === "") {
        $errors[] = "Enter your arrival date!";
    }

    if($departure === "") {
        $errors[] = "Enter your departure date!";
    }
       
    if(!$errors) {
        // check availability
        $availabilityCheck = $database->prepare('SELECT * FROM rooms WHERE room_type = :roomType AND room_number NOT IN (SELECT room_number FROM bookings WHERE arrival < :departure AND departure > :arrival) LIMIT 1;');

        $availabilityCheck->bindParam(':roomType', $roomType, PDO::PARAM_STR);
        $availabilityCheck->bindParam(':departure', $departure, PDO::PARAM_STR);
        $availabilityCheck->bindParam(':arrival', $arrival, PDO::PARAM_STR);

        $availabilityCheck->execute();
        $room = $availabilityCheck->fetch(PDO::FETCH_ASSOC);
        if($room) {
        $roomNumber = $room['room_number'];

        // Total price calculation
        $arrivalDate = new DateTime($arrival);
        $departureDate = new DateTime($departure);
        $nights = $arrivalDate->diff($departureDate)->days;
        $pricePerNight = $room['price'];
        $totalRoomPrice = $nights * $pricePerNight;

        // Total price for hotel nights and chosen features
        $totalPrice = $totalRoomPrice + $featuresTotal;

        // Validate transfer code
        $data = [
            "transferCode" => $transferCode,
            "totalCost" => $totalPrice
        ];

        $opts = [
            "http" => [
                "method" => "POST",
                "header" => "Content-Type: application/json\r\n",
                "content" => json_encode($data)
            ]
        ];
        $context = stream_context_create($opts);
        $url = 'https://www.yrgopelag.se/centralbank/transferCode';
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            $errors[] = "There was an error while validating your transfer code.";
        } else {
            $result = json_decode($response, true);
            if (isset($result['error'])) {
                $errors[] = $result['error'];
            } else {
                // insert into database if the booking is accepted
                $statement = $database->prepare('INSERT INTO bookings (name, room_number, arrival, departure) VALUES (:name, :roomNumber, :arrival, :departure)');
                $statement->bindParam(':name', $name, PDO::PARAM_STR);
                $statement->bindParam(':roomNumber', $roomNumber, PDO::PARAM_STR);
                $statement->bindParam(':arrival', $arrival, PDO::PARAM_STR);
                $statement->bindParam(':departure', $departure, PDO::PARAM_STR);

                $statement->execute();

                echo "Your reservation has been made!";
            }
        }
        } else {
            echo "Sorry. There is no available room of your selected room type for those dates.";
        }
    } else {
        foreach ($errors as $error) {
            echo $error;
        }
    }
};

// header('Location: /index.php');