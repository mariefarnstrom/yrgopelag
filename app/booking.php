<?php
declare(strict_types=1);

$database = new PDO('sqlite:' . __DIR__ . '/../hotel.db');
$errors = [];

if(isset($_POST['name'], $_POST['transferCode'], $_POST['arrival'], $_POST['departure'], $_POST['roomType'])) {
    $name = trim($_POST['name']);
    $transferCode = trim($_POST['transferCode']);
    $arrival = $_POST['arrival'];
    $departure = $_POST['departure'];
    $roomType = $_POST['roomType'];

    if($name === "") {
        $errors[] = "Please enter your name!";
    }

    if($transferCode === "") {
        $errors[] = "Please enter a valid transfer code!";
    } else {
        // vlidate transfer code
        
    }

    if($arrival === "") {
        $errors[] = "Enter your arrival date!";
    }

    if($departure === "") {
        $errors[] = "Enter your departure date!";
    }
       
    if(!$errors) {
        // check availability
        $availabilityCheck = $database->prepare('SELECT room_number FROM rooms WHERE room_type = :roomType AND room_number NOT IN (SELECT room_number FROM bookings WHERE arrival < :departure AND departure > :arrival) LIMIT 1;');

        $availabilityCheck->bindParam(':roomType', $roomType, PDO::PARAM_STR);
        $availabilityCheck->bindParam(':departure', $departure, PDO::PARAM_STR);
        $availabilityCheck->bindParam(':arrival', $arrival, PDO::PARAM_STR);

        $availabilityCheck->execute();
        $roomNumber = $availabilityCheck->fetch(PDO::FETCH_ASSOC);
        if($roomNumber) {
        $roomNumber = $roomNumber['room_number'];
        var_dump($roomNumber);

        // insert into database if the booking is accepted
        $statement = $database->prepare('INSERT INTO bookings (name, room_number, arrival, departure) VALUES (:name, :roomNumber, :arrival, :departure)');
        $statement->bindParam(':name', $name, PDO::PARAM_STR);
        $statement->bindParam(':roomNumber', $roomNumber, PDO::PARAM_STR);
        $statement->bindParam(':arrival', $arrival, PDO::PARAM_STR);
        $statement->bindParam(':departure', $departure, PDO::PARAM_STR);

        $statement->execute();

        echo "Your reservation has been made!";
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