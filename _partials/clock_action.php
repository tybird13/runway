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
$NOW = $date_time->format("Y-m-d H:i:s");

require_once 'DatabaseManager.Class.php';
$DM = new DatabaseManager();

// UIN: University Identification Number
if (isset($_POST['UIN'])) {
    $UIN = $_POST['UIN'];
} else {
    $response['errorCode'] = 1;
    $response['errorMsg'] = "Invalid UIN";
    echo json_encode($response);
    die();
}

// CHECK TO SEE IF THEY HAVE CLOCKED IN/OUT PREVIOUSLY IN AN APPROPRIATE WAY (DID THEY LOG OUT BEFORE TRYING TO LOG IN)
function check_logged_out($in, $out)
{
    if ($in == null || $out == null) {
        // this is the first log!!!
        return false;
    } else {
        return (strtotime($in) <= strtotime($out));
    }
}

function verify_hours($in_time_string, $out_time_string)
{
    // THIS FUNCTION VERIFIES THAT THIS IS NOT THE FIRST TIME THE USER IS CLOCKING IN, AND THAT THE USER HAS NOT BEEN
    // LOGGED IN FOR MORE THAN 12 HOURS.
    if (($in_time_string != NULL && $in_time_string != 'NULL') && ($out_time_string != NULL && $out_time_string != 'NULL')) {
        return ((strtotime($out_time_string) - strtotime($in_time_string)) / 3600) <= 12;
    } else {
        return false;
    }
}

function write_to_file($str, $UIN)
{

    $file_path = $_SERVER['DOCUMENT_ROOT'] . "/log/" . $UIN . ".log";
    if (($file = fopen($file_path, 'a')) === false) {
        throw new Exception('Failed to open file');
    }
    // WRITE THE STRING TO THE FILE
    fwrite($file, $str);
    fclose($file);

}


function flag_abandoned_punch($UIN)
{
    $file = $_SERVER['DOCUMENT_ROOT'] . "/log/" . $UIN . ".log"; // this is the log file
    if (($h = fopen($file, 'r+')) === false) {
        throw new Exception('Failed to open file');
    }

    $sz = filesize($file);
    $fp = 0;
    for ($n = -2; $n >= -$sz; $n--) {
        fseek($h, $n, SEEK_END);
        $c = fgetc($h);

        if ($c == "\n" || $c == "\r") {
            $fp = $n + 1;
            break;
        }
    }

    ftruncate($h, $sz + $fp);

    fclose($h);
}

function abandon_punch($UIN, $DM, $NOW)
{
    // REPLACE THE ABANDONED PUNCH WITH THE FLAG STRING, AND RESET THE LAST_CLOCK VALUES IN DB.
    flag_abandoned_punch($UIN);
    $str = "*****\n"; // THIS STRING WILL BE THE STRING WE USE TO SEARCH FOR ABANDONED PUNCHES IN THE FILE
    write_to_file($str, $UIN);

    return $DM->updateDatabase("UPDATE users SET last_clock_in = ?, last_clock_out = ?, clocked_in = 0 WHERE UIN = ?",
        array($NOW, $NOW, $UIN));

}

function diff_hours($in_time_string, $out_time_string)
{
    return (strtotime($out_time_string) - strtotime($in_time_string)) / 3600;
}


$time = $DM->accessDatabase("SELECT last_clock_in, last_clock_out, clocked_in, total_hours FROM users WHERE UIN=?", array($UIN));


// FUNCTIONS THAT WILL MAP THE THE BUTTONS ON THE STUDENT DASHBOARD, AND THE ADMINISTRATION 'EDIT ACCOUNT' PAGES.

if (isset($_POST['function'])) {

    switch ($_POST['function']) {
        case "clock_in": // FUNCTION TO CLOCK THE USER IN (CALLED BY STUDENT)

            // TODO: REWRITE THIS WHOLE CLUSTERFUCK
            if ($time['clocked_in']) {
                if (check_logged_out($time['last_clock_in'], $time['last_clock_out'])) {
                    // format the current date and time to match the MYSQL format for database insertion
                    $str = sprintf("%s,", $date_time->format("Y-m-d H:i:s"));
                    $response['update_status'] = $DM->updateDatabase("UPDATE users SET last_clock_in = ?, clocked_in = ? WHERE UIN = ?", array($str, 1, $UIN));
                    $_SESSION['clocked_in'] = 1;
                    write_to_file($str, $UIN);

                } else {
                    // DEAL WITH THE FACT THAT THEY DIDN'T SIGN OUT BEFORE
                    abandon_punch($UIN, $DM, $NOW);
                }

            } else {
                $str = sprintf("%s,", $date_time->format("Y-m-d H:i:s"));
                $response['update_status'] = $DM->updateDatabase("UPDATE users SET last_clock_in = ?, clocked_in = ? WHERE UIN = ?", array($str, 1, $UIN));
                $_SESSION['clocked_in'] = 1;
                write_to_file($str, $UIN);
            }

            break;

        case "clock_out": // FUNCTION TO CLOCK THE USER OUT (CALLED BY STUDENT)

            if ($time['clocked_in']) {

                // check to see if the clock out time is greater than or equal to 12 hours...
                if (verify_hours($time['last_clock_in'], $date_time->format("Y-m-d H:i:s"))) {

                    // format the current date and time to match the MYSQL format for database insertion
                    $str = sprintf("%s\n", $date_time->format("Y-m-d H:i:s"));
                    $hours = $time['total_hours'] + diff_hours($time['last_clock_in'], $date_time->format("Y-m-d H:i:s"));
                    $response['update_status'] = $DM->updateDatabase(
                        "UPDATE users SET last_clock_out = ?, clocked_in = ?, total_hours = ? WHERE UIN = ?",
                        array($str, 0, $hours, $UIN));
                    $_SESSION['clocked_in'] = 0;
                    write_to_file($str, $UIN);

                } else {
                    $response['errorCode'] = 2; // they may have forgotten to clock out before...
                    abandon_punch($UIN, $DM, $NOW);
                    $response['errorMsg'] = $time['last_clock_in'];
                }

            } else {
                $response['errorCode'] = 5;
                $response['errorMsg'] = "You are already logged out";

            }

            break;

        case "fix_log":
            // FUNCTION TO FIX AN ABANDONED PUNCH IN THE LOG FILE (FORGOT TO LOG OUT ONE DAY...)
            // THIS FUNCTION IS CALLED BY THE ADMIN CONSOLE (EDIT_ACCOUNT)

            // OPEN THE LOG FILE
            if (isset($_POST['in']) && isset($_POST['out'])) {

                $date_in = DateTime::createFromFormat("m/d/Y h:i A", $_POST['in'], $TIMEZONE);
                $date_out = DateTime::createFromFormat("m/d/Y h:i A", $_POST['out'], $TIMEZONE);
                $hours = (doubleval($date_out->format("U")) - doubleval($date_in->format("U"))) / 3600;
                $file = $_SERVER['DOCUMENT_ROOT'] . "/log/" . $UIN . ".log"; // this is the log file
                $punch = [$date_in->format("Y-m-d H:i:s"), $date_out->format("Y-m-d H:i:s")];

                // create the array that will store the punches
                if(($h = fopen($file, 'r+')) == false){
                    throw new Exception("PROBLEM");
                }
                $tmp_array = array();
                if($h){
                    while(!feof($h)){

                        array_push($tmp_array, fgetcsv($h));
                    }
                    fclose($h);
                }


                // get the index of the missed punch ('*****')
                $index = 0;
                foreach($tmp_array as $key => $element){
                    if($element[0] == "*****"){
                        $tmp_array[$key] = $punch;
                        break;
                    }
                }

                // rewrite the file
                if(($h = fopen($file, 'r+')) == false){
                    throw new Exception("PROBLEM");
                }

                foreach ($tmp_array as $fields){
                    if($fields != false){
                        fputcsv($h, $fields);
                    }
                }




                // MANUALLY ADD TIME TO THE DATABASE &
                // RESET THE LOG, IE: CLOCKED_IN -> 0, LAST_CLOCK_IN, LAST_CLOCK_OUT
                $total_hours = $DM->accessDatabase("SELECT total_hours FROM users WHERE UIN = ?",
                    array($_POST['UIN']));
                $total_hours = $total_hours['total_hours'] += $hours;
                $response['update_status'] = $DM->updateDatabase("UPDATE users SET clocked_in = 0, last_clock_in = ?,
                      last_clock_out = ?, total_hours = ? WHERE UIN = ?", array($NOW, $NOW, $total_hours, $UIN));
            } else {
                $response['update_status'] = 1;
            }


    break;

default:
    $response['errorMsg'] = "SOMETHING WENT WRONG";
    $response['errorCode'] = 4;
}

if (isset($response['update_status']) && $response['update_status'][0] == '00000') {
    $response['errorCode'] = 0;
}


echo json_encode($response);
}

