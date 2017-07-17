<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 7/13/2017
 * Time: 1:45 PM
 */

//require_once '_partials/imports.php';
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/_partials/DatabaseManager.Class.php';

header("Content-type: application/json");

$DM = new DatabaseManager();
$response = array();
$response['TEST'] = $_POST['function'];


// FUNCTION TO VERIFY INFO IN THE DATABASE AGAINST ACCEPTED STUDENTS IN THE RUNWAY PROGRAM
if(isset($_POST['function']) && $_POST['function'] == 'check_info'){

    // check to make sure all fields have been filled out
    if((!empty($_POST['fname']) && $_POST['fname'] !== 'undefined') &&
        (!empty($_POST['lname']) && $_POST['lname'] !== 'undefined') &&
        (!empty($_POST['email']) && $_POST['email'] !== 'undefined')){

        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $email = $_POST['email'];
    } else {
        $response['errorCode'] = 1;
        $response['errorMsg'] = "One or more of the fields were blank or undefined.";
        echo json_encode($response);
        die();
    }

// make sure the UIN was transferred.
    $UIN = 0;
    if(isset($_POST['UIN']) && $_POST['UIN'] != NULL){
        $UIN = $_POST['UIN'];
    } else {
        $response['errorCode'] = 1;
        $response['errorMsg'] = "<p class='text-danger'>UIN Undefined!</p>";
        echo json_encode($response);
        die();
    }
    $response['uin'] = $UIN;



// get all the information from the database
    $student = $DM->accessDatabase("SELECT * FROM accepted_students WHERE UIN=?", array($UIN));

    if($student){
        // check to make sure the info matches
        if(strcmp(strtolower($fname), strtolower($student['fname'])) == 0 &&
            strcmp(strtolower($lname), strtolower($student['lname'])) == 0 &&
            strcmp(strtolower($email), strtolower($student['eagle_mail'])) == 0){

            $response['code'] = "wtf?: " . $student['fname'] . ", " . $student['lname'] . ", " . $student['eagle_mail'];

            $response['code'] = "SUCCESS";

            // HAVE THEM UPDATE THE PASSWORD
            $response['errorCode'] = 0;

        } else {
            //$response['code'] = "FAILED: " . $student['fname'] . ", " . $student['lname'] . ", " . $student['eagle_mail'];
            $response['errorCode'] = 1;
            $response['errorMsg'] = "The information you entered does not match our records.";
            echo json_encode($response);

            die();
        }

    } else {
        $response['errorCode'] = 1;
        $response['errorMsg'] = "UIN not in database";
        echo json_encode($response);
        die();
    }

}

//  FUNCTION TO ADD A NEW PASSWORD TO THE DATABASE
if(isset($_POST['function']) && $_POST['function'] == 'add_password'){
    // check to see if the value has been passed correctly
    $pass = isset($_POST['pass']) ? $_POST['pass'] : NULL;
    $UIN = isset($_POST['UIN']) ? $_POST['UIN'] : NULL;

    if($pass == NULL){
        $response['errorCode'] = 1;
        $response['errorMsg'] = "Please enter a password";
        die();
    }
    if($UIN == NULL){
        $response['errorCode'] = 1;
        $response['errorMsg'] = "UIN Invalid";
        echo json_encode($response);
        die();
    }
    // add the password to the database
    $password = password_hash($pass, PASSWORD_DEFAULT);
    $student = $DM->accessDatabase("SELECT * FROM accepted_students WHERE UIN=?", array($UIN));

    $response['password-create'] = $DM->updateDatabase(
        "INSERT INTO users (UIN, fname, lname, eagle_mail, pass) VALUES (?, ?, ?, ?, ?)",
        array($UIN, $student['fname'], $student['lname'], $student['eagle_mail'], $password)
    );

    // create the log file for the user
    //try{
        $log_path = $_SERVER['DOCUMENT_ROOT'] . "/log/";
        $user_log = fopen($log_path . $UIN . ".log", 'a');
        //$response['file'] = $user_log;
        fclose($user_log);

        $_SESSION['UIN'] = $UIN;
        $_SESSION['fname'] = $student['fname'];
        $_SESSION['lname'] = $student['lname'];
        $_SESSION['email'] = $student['eagle_mail'];


        $response['errorCode'] = 0;
        //var_dump($response);
    //} catch (Exception $e){
    //    $response['ERROR'] = $e->getTraceAsString();
    //    $response['errorCode'] = 1;
    //    echo $e->getTrace();
    //}

}

echo (json_encode($response));
?>

