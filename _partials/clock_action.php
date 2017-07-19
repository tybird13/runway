<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 7/16/2017
 * Time: 4:18 PM
 */

require_once 'cookie.php';
$response = array();
$TIMEZONE = new DateTimeZone('America/New_York');
$str = "";
$date_time = new DateTime();
$date_time->setTimezone($TIMEZONE);

require_once 'DatabaseManager.Class.php';
$DM = new DatabaseManager();

// UIN: University Identification Number
if(isset($_POST['UIN'])){
    $UIN = $_POST['UIN'];
} else {
    $response['errorCode'] = 1;
    $response['errorMsg'] = "Invalid UIN";
    echo json_encode($response);
    die();
}

// CHECK TO SEE IF THEY HAVE CLOCKED IN/OUT PREVIOUSLY IN AN APPROPRIATE WAY (DID THEY LOG OUT BEFORE TRYING TO LOG IN)
function check_logged_out($in, $out){

    if($in != NULL && $out == NULL){
        return false;
    } else if($in == NULL && $out != NULL){
        return false;
    } else {
        if($in <= $out){
            return true;
        } else {
            return false;
        }
    }
}

function diff_hours(string $in_time_string, string $out_time_string) {
//    $in_time = new DateTime($in_time_string);
//    $out_time = new DateTime($out_time_string);
//    $diff = $in_time->diff($out_time, true);
//    $minutes = intval($diff->format("%I"));
//    $hours = intval($diff->format(("%H")));
//
//    // convert everything to minutes
//    $hours *= 60;
//    $minutes += $hours;
//
//    // convert everything back into hours (double form)
//    $hours = (double) $minutes / 60.0;
    return (strtotime($out_time_string) - strtotime($in_time_string)) / 3600;

}


$time = $DM->accessDatabase("SELECT last_clock_in, last_clock_out, clocked_in, total_hours FROM users WHERE UIN=?", array($UIN));


// CLOCK IN FUNCTION
if (isset($_POST['function']) && $_POST['function'] == "clock_in") {

    if (!$time['clocked_in']) {
        if (check_logged_out($time['last_clock_in'], $time['last_clock_out'])) {

            // format the current date and time to match the MYSQL format for database insertion
            $str = sprintf("%s,", $date_time->format("Y-m-d H:i:s"));
            $response['update_status'] = $DM->updateDatabase("UPDATE users SET last_clock_in = ?, clocked_in = ? WHERE UIN = ?", array($str, 1, $UIN));
            $_SESSION['clocked_in'] = 1;

        } else {
            // DEAL WITH THE FACT THAT THEY DIDN'T SIGN OUT BEFORE
            $date_missed = DateTime::createFromFormat("Y-m-d H:i:s", $time['last_clock_in']);
            $response['date_missed'] = $date_time->format("D, M dS Y");
            $response['errorCode'] = 2;

        }

    } else {
        $date_missed = DateTime::createFromFormat("Y-m-d H:i:s", $time['last_clock_in']);
        $response['date_missed'] = $date_time->format("D, M dS Y");
        $response['errorCode'] = 2;
    }
}

// CLOCK OUT FUNCTION
if(isset($_POST['function']) && $_POST['function'] == "clock_out"){

    if($time['clocked_in']){

        // check to see if the clock out time is greater than or equal to 12 hours...
        $test_time = new DateTime($time['last_clock_in']); // create a DateTime object from the last clocked in time
        $diff = $date_time->diff($test_time);
        if(diff_hours($time['last_clock_in'], $date_time->format("Y-m-d H:i:s")) <= 12){

            // format the current date and time to match the MYSQL format for database insertion
            $str = sprintf("%s\n", $date_time->format("Y-m-d H:i:s"));
            $hours = $time['total_hours'] + diff_hours($time['last_clock_in'], $date_time->format("Y-m-d H:i:s"));
            $response['update_status'] = $DM->updateDatabase(
                "UPDATE users SET last_clock_out = ?, clocked_in = ?, total_hours = ? WHERE UIN = ?",
                array($str, 0, $hours, $UIN));
            $_SESSION['clocked_in'] = 0;

        } else {
            $response['errorCode'] = 2; // they may have forgotten to clock out before...
            $response['date_missed'] = date_create($time['last_clock_in'], new DateTimeZone("America/New_York"))->format("D, M dS Y");
        }

    } else {
        // DEAL WITH THE FACT THAT THEY DIDN'T SIGN OUT BEFORE
        $response['errorCode'] = 5;
        $response['errorMsg'] = "You are already logged out";

    }
}


// CLOCK OUT FUNCTION
// if the user has forgotten to clock out previously.
if(isset($_POST['function']) && $_POST['function'] == "fix_log"){

    // format the current date and time to match the MYSQL format for database insertion
    if(isset($_POST['date_missed'])){

        $date_missed = DateTime::createFromFormat("D, M dS Y h:i A", $_POST['date_missed']);

        // MAKE SURE THE TIME THEY ENTER IS GREATER THAN THE TIME THEY CLOCKED IN
        if(strtotime($time['last_clock_in']) < strtotime($date_missed->format("Y-m-d H:i:s"))){

            $response['date_recorded'] = $date_missed->format("m/d/Y h:i A");
            $str = sprintf("%s\n", $date_missed->format("Y-m-d H:i:s"));
            $hours = $time['total_hours'] + diff_hours($time['last_clock_in'], $date_missed->format("Y-m-d H:i:s"));
            $response['update_status'] = $DM->updateDatabase(
                "UPDATE users SET last_clock_out = ?, clocked_in = ?, total_hours = ? WHERE UIN = ?",
                array($str, 0, $hours, $UIN));
            $_SESSION['clocked_in'] = 0;

        } else {
            $response['errorCode'] = 4;
            $response['errorMsg'] = "Clock out must be later than clock in.";
        }

    }

}

$response['date-time'] = $str;
$file_path = $_SERVER['DOCUMENT_ROOT'] . "/log/" . $UIN . ".log";
$file = fopen($file_path, 'a');

// WRITE THE STRING TO THE FILE
fwrite($file, $str);
fclose($file);

if(isset($response['update_status']) && $response['update_status'][0] == '00000'){
    $response['errorCode'] = 0;
}

echo json_encode($response);
?>




