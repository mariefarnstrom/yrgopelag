<?php
declare(strict_types=1);

require __DIR__ . '/autoload.php';

function getAvailableDates(PDO $db, string $roomType, int $month, int $year): array {
    $availableDays = [];
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

        // Get total rooms of this room type
        $totalStatement = $db->prepare(
            "SELECT COUNT(*) FROM rooms WHERE room_type = :roomType"
        );
        $totalStatement->bindParam(':roomType', $roomType);
        $totalStatement->execute();
        $totalRooms = (int)$totalStatement->fetchColumn();

        // Get booked rooms for each date
        $bookedStatement = $db->prepare(
            "SELECT COUNT(DISTINCT rooms.room_number) FROM bookings
             JOIN rooms ON rooms.room_number = bookings.room_number
             WHERE rooms.room_type = :roomType AND arrival <= :date AND departure > :date"
        );

        $bookedStatement->bindParam(':roomType', $roomType);
        $bookedStatement->bindParam(':date', $date);
        $bookedStatement->execute();
        $bookedRooms = (int)$bookedStatement->fetchColumn();

        if ($bookedRooms < $totalRooms) {
            $availableDays[] = $day;
        }
    }

    return $availableDays;
}

function getCalendar(array $available = [], ?int $month = null, ?int $year = null, bool $weekStartsMonday = true): string
{
    if ($month === null) $month = (int)date('n');
    if ($year === null) $year = (int)date('Y');
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $dt = new DateTime("$year-$month-01");
    $startWeekday = (int)$dt->format('w');
    if ($weekStartsMonday) {$startWeekday = ($startWeekday + 6)%7;}

        $html = '<section class="calendar">';

    for ($i = 0; $i < $startWeekday; $i++) {
        $html .= '<div class= "day empty"></div>';
    }
    for ($i = 1; $i <= $daysInMonth; $i++) :
        $startDay = $startWeekday;
        $Weekday = ($startDay + ($i - 1)) % 7;
        $isWeekend = ($Weekday === 5 || $Weekday === 6);
        $classes = 'day' . (in_array($i, $available) ? ' available' : '') . ($isWeekend ? ' weekend' : '');
        $html .= '<div class="' . $classes . '">' . $i . '</div>';
    endfor;
    $html .= '</section>';
    return $html;
}