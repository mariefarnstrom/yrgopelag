<?php
$booked = [2, 6, 19, 27, 28];
function getCalendar(array $booked = [], ?int $month = null, ?int $year = null, bool $weekStartsMonday = true): string
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
        $classes = 'day' . (in_array($i, $booked) ? ' booked' : '') . ($isWeekend ? ' weekend' : '');
        $html .= '<div class="' . $classes . '">' . $i . '</div>';
    endfor;
    $html .= '</section>';
    return $html;
}