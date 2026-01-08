Outset Shores – Hotel website

This project is a hotel booking website created as part of a web development assignment. Guests can view available rooms, select dates, add optional features, and make a reservation using a transfer code provided by the central bank API.

The system handles availability checks, price calculation, and booking confirmation while keeping data secure using prepared statements and output escaping.


Features:

    * View room types with prices (Economy, Standard, Luxury)

    * Calendar showing available dates per room type

    * Booking form with:

        Arrival and departure dates

        Room type selection

        Optional additional features

    * Transfer code validation via Central Bank API

    * Receipt creation and deposit via Central Bank API

    * Booking confirmation page

    * Error handling with user-friendly messages

Technologies Used:

    PHP

    PDO for database access

    SQLite databases

    HTML & CSS

    Sessions for error handling and confirmation data

    External API (Yrgopelag Central Bank)


The project uses two separate SQLite databases:

    1. Hotel Database

        Contains:

        * rooms (room_type, room_number, price)

        * bookings (name, room_number, arrival, departure)

        This database is used for: Availability checks, Price calculation, Storing confirmed bookings

    2. Features Database

        Contains:

        * features (feature, price, category, rank, active)

        This database allows features to be edited independently without affecting existing bookings.


How to Run the Project:

    * Clone or download the repository from GitHub and place it in your local web server directory.

    * Install dependencies:

        This project uses the vlucas/phpdotenv package.

        Install dependencies using Composer: composer install

        Installation instructions for the package can be found here: https://github.com/vlucas/phpdotenv

    * Add environment variables:

        Create a .env file in the project root and add your API key:

        API_KEY=your_api_key_here


    * Run the project:

        Start a local PHP server and open the project in your browser.