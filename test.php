<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 7/18/2017
 * Time: 11:38 AM
 */


function diff_hours(string $in_time_string, string $out_time_string) {
//    $in_time = new DateTime($in_time_string);
//    $out_time = new DateTime($out_time_string);
//
//    $diff = $in_time->diff($out_time, true);
//    var_dump($diff);
//    // WE HAVE TO CONVERT EVERYTHING MANUALLY
//
//    // CONVERT MONTHS TO HOURS
//    $months_hours = doubleval($diff->format("%m")) * 730;
//    // CONVERT DAYS TO HOURS
//    $days_hours = doubleval($diff->format("%d"));
//    // CONVERT MINUTES TO HOURS
//    $minutes_hours = doubleval($diff->format("%i")) / 60;
//    // CONVERT SECONDS TO HOURS
//    $seconds_hours = doubleval($diff->format("%s")) / (60*60);
//
//    // add all the hours up
//    $hours = $months_hours + $days_hours + $minutes_hours + $seconds_hours;



    return (strtotime($out_time_string) - strtotime($in_time_string)) / 3600;



}



$in = "2017-07-18 12:00:00";

$out = "07/18/2017 12:01 PM";

echo diff_hours($in, $out);

