<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 8/24/2017
 * Time: 8:19 PM
 */

$response = array();
require_once "DatabaseManager.Class.php";
$DM = new DatabaseManager();

if(isset($_POST['function'])){
    switch($_POST['function']){

        case "change password": // the change password function
            if(isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['UIN'])){
                $student = $DM->accessDatabase("SELECT * FROM users WHERE UIN = ?", array($_POST['UIN']));

                $current_password = $_POST['current_password'];
                $new_password = $_POST['new_password'];

                //if(password_verify($current_password, $student['pass'])){
                    $response["update message"] = $DM->updateDatabase("UPDATE users SET pass = ? WHERE UIN = ?",
                        array(password_hash($new_password, PASSWORD_DEFAULT), $_POST['UIN']));
                    $response['errorCode'] = 0;

//                } else {
//                    $response['errorCode'] = 1;
//                    $response['errorMsg'] = "Your old password was incorrect.";
//                }
            } else {
                $response['errorCode'] = 1;
                $response['errorMsg'] = "Passwords or UIN Not Defined";
            }
            break;

        case "change email":

            break;
        default:
            $response['errorCode'] = 1;
            $response['errorMsg'] = "Function Not Defined";
            break;

    }

    echo json_encode($response);

}
?>