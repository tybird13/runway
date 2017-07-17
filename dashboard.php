<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 7/10/2017
 * Time: 1:52 PM
 */
require_once '_partials/imports.php';
require_once '_partials/DatabaseManager.Class.php';

$DM = new DatabaseManager();
$student = $DM->accessDatabase("SELECT * From users WHERE UIN=?", array($_SESSION['UIN']));

$current_DateTime = new DateTime();
$current_DateTime->setTimezone(new DateTimeZone("America/New_York"));

?>

<title><?php echo $_SESSION['UIN'] ?> | Dashboard</title>

</head>

<body>
<?php require_once '_partials/navbar.php'?>
<div class="container">
    <div class="row">
        <div class="col-sm-offset-2 col-sm-8 text-center">
            <div class="msg-target"></div>
            <h1>Welcome, <?php echo "{$_SESSION['fname']} {$_SESSION['lname']}"?></h1>
<!--            <h4>Current DateTime: --><?php //echo $current_DateTime->format("m/d/Y h:i A") ?><!--</h4>-->
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <h4>Total Hours To Date: <?php echo $student['total_hours']?> hours</h4>
        </div>
        <div class="col-sm-6">

        </div>
    </div>

    <div class="row">
        <div class="col-sm-offset-2 col-sm-8">
            <div class="log-hours-box">
                <div class="row">
                    <div class="col-sm-offset-2 col-sm-8">
                        <div class="text-center">
                            <button id="clock_in" class="btn btn-primary btn-lg">Clock In</button>
                            <button id="clock_out" class="btn btn-danger btn-lg">Clock Out</button>
                            <!-- div to handle the event that the user forgets to log out -->
                            <div id="time_input_section" class="hide">
                                <h4>It seems that you forgot to log out on ## DATE ##. Please enter the time you left the ETI.</h4>
                                <form class="form-inline">
                                    <label class="control-label">Time: eg 12:00 PM</label>
                                    <input class="form-control" id="time" name="time">
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {

        $('#clock_in').click(function () {

            if(confirm("Are you sure you would like to clock in?")){
                var xhr = new XMLHttpRequest();
                var fd = new FormData();

                fd.append('UIN', '<?php echo $_SESSION['UIN'] ?>');

                xhr.open('post', '_partials/clock_in_action.php');
                xhr.send(fd);

                xhr.onload = function(){
                    //console.log(this.responseText);
                    var data = JSON.parse(this.responseText);

                    if(data['errorCode'] === 2){
                        // THIS MEANS THAT THE USER FORGOT TO LOG OUT PREVIOUSLY

                        // hide the log in button, and replace with an input asking for the time the user
                        // left on the specified date.

                        // take the time the user types and input it into the database as a clock-out
                        // when the user clicks clock-out

                        // update the user database with the new clock out info

                        // refresh the page

                        $('#clock_in').hide();
                        $('#time_input_section').removeClass('hide');


                    } else if(data['errorCode'] === 0){
                        $('.msg-target').html('<h2 class="alert alert-success">You Have Successfully Clocked In</h2>');

                    }


                }            }

        })
    })
</script>
</body>
</html>