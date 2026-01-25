<?php
declare(strict_types=1);
session_start();

require __DIR__ . '/autoload.php';
require __DIR__ . '/functions.php';

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
        $chosenFeature = $database->prepare('SELECT id, feature, price FROM features WHERE id = :feature');
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

    if($arrival >= $departure) {
        $errors[] = "Arrival must be before departure!";
    }
       
    if(!$errors) {
        // check availability
        $availabilityCheck = $database->prepare('SELECT * FROM rooms WHERE room_type = :roomType AND room_number NOT IN (SELECT room_number FROM bookings WHERE arrival < :departure AND departure > :arrival) LIMIT 1;');

        $availabilityCheck->bindParam(':roomType', $roomType, PDO::PARAM_STR);
        $availabilityCheck->bindParam(':departure', $departure, PDO::PARAM_STR);
        $availabilityCheck->bindParam(':arrival', $arrival, PDO::PARAM_STR);

        $availabilityCheck->execute();
        $room = $availabilityCheck->fetch(PDO::FETCH_ASSOC);
        if(!$room) {
            $errors[] = "Sorry. There is no available room of your selected room type for those dates.";
        } else {

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
            $validationData = [
                "transferCode" => $transferCode,
                "totalCost" => $totalPrice
            ];

            $validationResult = postJson('https://www.yrgopelag.se/centralbank/transferCode', $validationData);

            if ($validationResult === false) {
                $errors[] = "There was an error while validating your transfer code.";
            } else if (!isset($validationResult['status']) || $validationResult['status'] !== 'success') {
                    $errors[] = "Transfer code validation failed.";
                } else {
                    // Post receipt to Central bank
                    $featureNames = [];
                    foreach ($features as $feature) {
                        $featureNames[] = $feature['feature'];
                    }

                    $receiptInfo = [
                        "user" => "Marie",
                        "api_key" => $apiKey,
                        "guest_name" => $name,
                        "arrival_date" => $arrival,
                        "departure_date"=> $departure,
                        "features_used" => $featureNames,
                        "star_rating" => 1
                    ];

                    $receiptResult = postJson('https://www.yrgopelag.se/centralbank/receipt', $receiptInfo);

                    if ($receiptResult === false) {
                        $errors[] = "There was an error creating the receipt";
                    } else if (!isset($receiptResult['status']) || $receiptResult['status'] !== 'success') {
                        $errors[] = $receiptResult['error'] ?? 'Receipt was rejected.';
                    } else {
                        // insert into database
                        $statement = $database->prepare('INSERT INTO bookings (name, room_number, arrival, departure) VALUES (:name, :roomNumber, :arrival, :departure)');
                        $statement->bindParam(':name', $name, PDO::PARAM_STR);
                        $statement->bindParam(':roomNumber', $roomNumber, PDO::PARAM_STR);
                        $statement->bindParam(':arrival', $arrival, PDO::PARAM_STR);
                        $statement->bindParam(':departure', $departure, PDO::PARAM_STR);

                        $statement->execute();

                        $statement = $database->prepare('INSERT INTO bookings_features (booking_id, feature_id) VALUES (:bookingId, :featureId)');
                        $bookingId = $database->lastInsertId();

                        foreach ($features as $feature) {
                            $statement->bindParam(':bookingId', $bookingId, PDO::PARAM_STR);
                            $statement->bindParam(':featureId', $feature['id'], PDO::PARAM_STR);
                            $statement->execute();
                        }

                        $depositData = [
                            "user" => "Marie",
                            "transferCode" => $transferCode,
                        ];
                        
                        $depositResult = postJson('https://www.yrgopelag.se/centralbank/deposit', $depositData);

                        if ($depositResult === false) {
                            $errors[] = "There was an error making the deposit";
                        } else if (!isset($depositResult['status']) || $depositResult['status'] !== 'success') {
                            $errors[] = $depositResult['error'] ?? 'Could not complete the deposit.';
                        } else {
                            // redirect to confirmation.php if the booking is confirmed
                            $_SESSION['confirmation'] = [
                                'guest_name' => $name,
                                'arrival' => $arrival,
                                'departure' => $departure,
                                'room_type' => $roomType,
                                'features' => $featureNames,
                                'total_price' => $totalPrice
                            ];
                            header('Location: confirmation.php');
                            exit;
                        }
                    }
                }
            
            }
        } 
    }
    // If there are errors redirect back to index.php
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: ../index.php#formErrors');
        exit;
    }