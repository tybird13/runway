<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 7/18/2017
 * Time: 4:08 PM
 */
require_once '_partials/cookie.php';
require_once '_partials/guard.php';
require_once '_partials/imports.php';
require_once '_partials/DatabaseManager.Class.php';
require_once 'scripts/button_functions.php';


// make sure the user has proper access credentials
if(!isset($_GET['UIN']) || !$_SESSION['is_admin'] || !isset($_SESSION['fname'])){
    header("Location: index.php");
}

try{
    $UIN = intval(htmlentities($_GET['UIN']));
} catch (Exception $e){
    die("WE HIT A SNAG");
}

$DM = new DatabaseManager();
$student = $DM->accessDatabase("SELECT * FROM users WHERE UIN = ?", array($UIN));
require_once '_partials/generate_student_report.php';

?>


<title><?php echo $student['UIN']?> | Student Report</title>

</head>
<body>
<?php require_once '_partials/navbar.php' ?>
<div class="container">
    <div class="row">
        <div class="col-xs-12">

        </div>

        <!-- Table of all log outs -->
        <div class="col-xs-12">
            <?php generate_report($student, $UIN); ?>
        </div>
    </div>
</div>

</body>
</html>