<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 7/16/2017
 * Time: 4:18 PM
 */

session_start();
$response = array();
$timezone = new DateTimeZone('America/New_York');
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
$time = $DM->accessDatabase("SELECT last_clock_in, last_clock_out FROM users WHERE UIN=?", array($UIN));

if(($time['last_clock_in'] != NULL)){
    if ($time['last_clock_in'] < $time['last_clock_out']) {
        $correct = true;
    } else {
        $correct = false;
    }
} else {
    // this means that this is the first time the user is clocking in, and it doesn't matter what the values are.
    $correct = true;
}

if($correct){

    $date_time = new DateTime();
    $date_time->setTimezone($timezone);

    // format the current date and time to match the MYSQL format for database insertion
    $str = sprintf("%s,", $date_time->format("Y-m-d H:i:s"));

    $response['date-time'] = $str;
    $file_path = $_SERVER['DOCUMENT_ROOT'] . "/log/" . $UIN . ".log";
    $file = fopen($file_path, 'a');

    // WRITE THE STRING TO THE FILE
    fwrite($file, $str);
    fclose($file);

    // update the database to reflect last login
    $response['update_status'] = $DM->updateDatabase("UPDATE users SET last_clock_in = ? WHERE UIN = ?", array($str, $UIN));

    $response['errorCode'] = 0;


} else {
    // DEAL WITH THE FACT THAT THEY DIDN'T SIGN OUT BEFORE
    $response['errorCode'] = 2;

}

echo json_encode($response);
?>




