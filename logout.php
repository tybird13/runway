<?php

require_once '_partials/cookie.php';

session_destroy();

header("Location: index.php");
?>
