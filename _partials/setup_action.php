<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 7/13/2017
 * Time: 1:45 PM
 */

//require_once '_partials/imports.php';
require_once 'cookie.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/_partials/DatabaseManager.Class.php';

header("Content-type: application/json");

$DM = new DatabaseManager();
$response = array();
$response['TEST'] = $_POST['function'];



//  FUNCTION TO ADD A NEW PASSWORD TO THE DATABASE
if (isset($_POST['function']) && $_POST['function'] == 'add_password') {

    // check to see if the value has been passed correctly
    $pass = isset($_POST['pass']) ? $_POST['pass'] : NULL;
    $UIN = isset($_POST['UIN']) ? $_POST['UIN'] : NULL;
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];

    if ($pass == NULL) {
        $response['errorCode'] = 1;
        $response['errorMsg'] = "Please enter a password";
        die();
    }
    if ($UIN == NULL) {
        $response['errorCode'] = 1;
        $response['errorMsg'] = "UIN Invalid";
        echo json_encode($response);
        die();
    }
    // add the password to the database
    $password = password_hash($pass, PASSWORD_DEFAULT);

    $response['password-create'] = $DM->updateDatabase(
        "INSERT INTO users (UIN, fname, lname, eagle_mail, pass) VALUES (?, ?, ?, ?, ?)",
        array($UIN, $fname, $lname, $email, $password)
    );

    // create the log file for the user
    //try{
    $log_path = $_SERVER['DOCUMENT_ROOT'] . "/log/";
    $user_log = fopen($log_path . $UIN . ".log", 'a');
    //$response['file'] = $user_log;
    fclose($user_log);

    $_SESSION['is_admin'] = 0;
    $_SESSION['UIN'] = $UIN;
    $_SESSION['fname'] = $fname;
    $_SESSION['lname'] = $lname;
    $_SESSION['email'] = $email;


    $response['errorCode'] = 0;

}

echo(json_encode($response));
?>

