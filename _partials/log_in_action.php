<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 7/13/2017
 * Time: 1:49 PM
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/_partials/DatabaseManager.Class.php';
$DM = new DatabaseManager();
$response = array();
$pass = $_POST['pass'];
$UIN = $_POST['UIN'];

$result = $DM->accessDatabase("SELECT * FROM users WHERE UIN = ?", array($UIN));
$test = password_verify($pass, $result['pass']);
if($test){
    $response['errorCode'] = 0;

    $_SESSION['UIN'] = $UIN;
    $_SESSION['fname'] = $result['fname'];
    $_SESSION['lname'] = $result['lname'];
    $_SESSION['email'] = $result['eagle_mail'];
} else {
    $response['errorCode'] = 1;
    $response['errorMsg'] = "Invalid Password";
}

echo json_encode($response);
?>