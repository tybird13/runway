<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 7/10/2017
 * Time: 1:52 PM
 */
require_once '_partials/cookie.php';
require_once '_partials/imports.php';
require_once '_partials/DatabaseManager.Class.php';
require_once '_partials/guard.php';
require_once '_partials/generate_student_report.php';

/*
 * BUTTON FUNCTIONS IS THE JAVASCRIPT FILE THAT CONTROLS THE BEHAVIOUR OF THE CLOCK IN AND CLOCK OUT BUTTONS
 * IT IS A PHP FILE SO THAT DATABASE VALUES CAN BE EASILY PASSED TO THE JAVASCRIPT FUNCTIONS.
 */
require_once 'scripts/button_functions.php';

$UIN = $_SESSION['UIN'];
$DM = new DatabaseManager();
$student = $DM->accessDatabase("SELECT * From users WHERE UIN=?", array($UIN));

$current_DateTime = new DateTime();
$current_DateTime->setTimezone(new DateTimeZone("America/New_York"));

?>

<title><?php echo $_SESSION['UIN'] ?> | Dashboard</title>
<script src="scripts/form_validation.js"></script>
</head>

<body>
<?php require_once '_partials/navbar.php'?>
<div class="container">
    <div class="row">
        <div class="col-sm-offset-2 col-sm-8 text-center">
            <div class="msg-target"></div>
            <h1>Welcome,
                <?php
                    echo "{$_SESSION['fname']} {$_SESSION['lname']}";
                    if($_SESSION['is_admin']){
                        echo " <strong>[admin]</strong>";
                    }
                ?></h1>


            <?php if(isset($_SESSION['fname'])): ?>
                <?php if($student['clocked_in']): ?>
                    <div id="status"><h3>Status: <span class="text-success">Clocked In</span></h3></div>
                <?php else: ?>
                    <div id="status"><h3>Status: <span class="text-danger">Clocked Out</span></h3></div>
                <?php endif;?>
            <?php endif ?>


        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-6">
            <h2 class="hour">Total Hours: <strong><?php echo sprintf("%.2f", $student['total_hours']) ?></strong>
                hours</h2>
        </div>
        <div class="col-sm-12 col-md-6">
            <h2 class="percent"><strong><?php echo (sprintf("%.2f", (($student['total_hours']/80.0) * 100)))
                    ?>%</strong> of 80 hours</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-offset-2 col-sm-8">
            <div class="log-hours-box">
                <div class="row">
                    <div class="col-sm-12 col-md-offset-1 col-md-10">
                        <div class="text-center">
                            <button id="clock_in" class="btn btn-primary btn-lg">Clock In</button>
                            <button id="clock_out" class="btn btn-danger btn-lg">Clock Out</button>
                            <!-- div to handle the event that the user forgets to log out -->
                            <div id="time_input_section" class="hide">
                                <h4 class="text-danger">It seems that you forgot to log out on <span
                                            id="date_missed"></span>. This time has been removed, please report to
                                    Scott Kelly to have the time restored.</h4>
                                <form id="time-form" class="form-inline">
                                    <div class="form-group">
                                        <label for="time" class="control-label">Time: eg 05:00 PM</label>
                                        <input class="form-control" id="time" name="time">
                                    </div>
                                    <div class="form-group">
                                        <button style="margin: 20px 0px" type="submit" class="btn btn-success">Fix Log</button>
                                        <span style="margin: 20px 0px" id="cancel" class="btn btn-warning">Cancel</span>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?php generate_report($student, $UIN) ?>
        </div>
    </div>
</div>
</body>
</html>