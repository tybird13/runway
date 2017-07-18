<?php
//$lifetime = strtotime("+5 seconds", 0);
//session_start();
//session_set_cookie_params($lifetime);

session_start([
    'cookie_lifetime' => 15*60 // 15 minutes
    //'name' => 'SESSION COOKIE THING'
])
?>
