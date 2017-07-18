<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 7/17/2017
 * Time: 6:15 PM
 */

if(!isset($_SESSION['fname'])){
    header("Location: index.php");
}