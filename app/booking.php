<?php
declare(strict_types=1);

$database = new PDO('sqlite:' . __DIR__ . '/../hotel.db');
$errors = [];

if(isset($_POST['name'], $_POST['transferCode'], $_POST['arrival'], $_POST['departure'], $_POST['roomType'])) {
    $name = $_POST['name'];
    $transferCode = $_POST['transferCode'];
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
        echo "Is the transfer code valid?";
    }

    if($arrival === "") {
        $errors[] = "Enter your arrival date!";
    }

    if($departure === "") {
        $errors[] = "Enter your departure date!";
    }
       
    if(!$errors) {
        echo "Your reservation has been made!";
    } else {
        foreach ($errors as $error) {
            echo $error;
        }
    }
};

// header('Location: /index.php');