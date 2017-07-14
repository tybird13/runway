<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 7/13/2017
 * Time: 1:49 PM
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/_partials/DatabaseManager.Class.php';
$DM = new DatabaseManager();

$pass = $_POST['pass'];
$UIN = $_POST['UIN'];

$result = $DM->accessDatabase("SELECT * FROM users WHERE UIN = ?", array($UIN));

$test = password_verify($pass, $result['password']);

var_dump($test);
?>