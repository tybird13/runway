<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 9/27/2017
 * Time: 4:13 PM
 */

require_once 'DatabaseManager.Class.php';
$DM = new DatabaseManager();

$response = array();

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

function diff_hours($in_time_string, $out_time_string)
{
    return (strtotime($out_time_string) - strtotime($in_time_string)) / 3600;
}


if (isset($_POST['UIN']) && isset($_POST['in']) && isset($_POST['out'])) {

    $UIN = $_POST['UIN'];
    $in = new DateTime($_POST['in'], new DateTimeZone("America/New_York"));
    $out = new DateTime($_POST['out'], new DateTimeZone("America/New_York"));

    $total_hours = $DM->accessDatabase("SELECT total_hours FROM users WHERE UIN = ?", array($UIN));
    $total_hours = $total_hours['total_hours'];
    $total_hours += diff_hours($in->format("m/d/Y H:i:s"), $out->format("m/d/Y H:i:s"));

    //var_dump($total_hours, $in, $out, $UIN);
    $DM->updateDatabase("UPDATE users SET last_clock_in = ?, last_clock_out = ?, total_hours = ? WHERE UIN = ?",
        array($in->format("Y-m-d H:m:i"), $out->format("Y-m-d H:m:i"), $total_hours, $UIN));


    // add the punch to the log file

    $str = "\n{$in->format('Y-m-d H:i:s')},{$out->format('Y-m-d H:i:s')}\n";
    write_to_file($str, $UIN);
    var_dump($str);
    $response['errorCode'] = 0;
    echo json_encode($response);

} else {
    $response['errorCode'] = 1;
    $response['errorMsg'] = "There was an error";
}

