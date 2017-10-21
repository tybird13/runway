<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 7/18/2017
 * Time: 11:38 AM
 */

require_once '_partials/DatabaseManager.Class.php';

$in = "2017-08-17 7:00 PM";
$out = "2017-08-17 11:00 PM";
$now = "2017-08-17 8:44 PM";

var_dump(strtotime("now") > strtotime($now));



