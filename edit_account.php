<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 8/24/2017
 * Time: 9:03 PM
 */
require_once "_partials/cookie.php"; // protection from those who are not authorized to view this page.
if(!$_SESSION['is_admin']){
    header("Location: index.php");
    die();
}
require_once "_partials/imports.php";
require_once "_partials/DatabaseManager.Class.php";

?>

<title>Edit User Info</title>
</head>
<body>
<?php require_once "_partials/navbar.php"?>
<div class="container">

    <div class="row">
        <div class="col-xs-12">
            <div class="alert alert-warning">
                <p><strong>This section is</strong> used to reset student passwords, and fix student hours. Each
                    section is handled and submitted separately. Each is its own form.
                </p>
            </div>
            <div id="msg-target"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="sign-in-box-no-align">
                <form class="clearfix" id="password_change_form">
                    <div class="text-center">
                        <h1>Update Student Password</h1>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="pass_UIN">Student UIN</label>
                        <input class="form-control" name="pass_UIN" id="pass_UIN">
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="new_password">New Password</label>
                        <input type="password" class="form-control" name="new_password" id="new_password">
                    </div>
                    <button style="float: right;" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>

        <div class="col-xs-12 col-md-6">
            <div class="sign-in-box-no-align">
                <form class="clearfix" id="fix_broken_hours_form">
                    <div class="text-center">
                        <h1>Fix Broken Hours</h1>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="hour_UIN">Student UIN</label>
                        <input class="form-control" name="hour_UIN" id="hour_UIN">
                    </div>
                    <div class="row">
                        <div class="col-sm-4 col-xs-12">
                            <div class="form-group">
                                <label class="control-label" for="date">Date Missed</label>
                                <input class="form-control" name="date" id="date">
                            </div>
                        </div>
                        <div class="col-sm-4 col-xs-12">
                            <div class="form-group">
                                <label class="control-label" for="time_in">Time IN</label>
                                <input class="form-control" name="time_in" id="time_in">
                            </div>
                        </div>
                        <div class="col-sm-4 col-xs-12">
                            <div class="form-group">
                                <label class="control-label" for="time_out">Time Out</label>
                                <input class="form-control" name="time_out" id="time_out">
                            </div>
                        </div>

                    </div>
                    <button style="float: right;" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="sign-in-box-no-align">
                <form class="clearfix" id="bypass_broken_hours">
                    <div class="text-center">
                        <h1>Add Hours (bypass flags)</h1>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="hour_UIN">Student UIN</label>
                        <input class="form-control" name="broken_hour_UIN" id="broken_hour_UIN">
                    </div>
                    <div class="row">
                        <div class="col-sm-4 col-xs-12">
                            <div class="form-group">
                                <label class="control-label" for="broken_date">Date Missed</label>
                                <input class="form-control" name="broken_date" id="broken_date">
                            </div>
                        </div>
                        <div class="col-sm-4 col-xs-12">
                            <div class="form-group">
                                <label class="control-label" for="broken_time_in">Time IN</label>
                                <input class="form-control" name="broken_time_in" id="broken_time_in">
                            </div>
                        </div>
                        <div class="col-sm-4 col-xs-12">
                            <div class="form-group">
                                <label class="control-label" for="broken_time_out">Time Out</label>
                                <input class="form-control" name="broken_time_out" id="broken_time_out">
                            </div>
                        </div>

                    </div>
                    <button style="float: right;" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {

        $('#date, #broken_date').datetimepicker({
            timepicker: false,
            format: 'm/d/Y'
        });

        $('#time_in, #time_out, #broken_time_in, #broken_time_out').datetimepicker({
            format: 'h:i A',
            datepicker: false
        });

        $('#password_change_form').submit(function (e) {
            e.preventDefault();
            //console.log(e);
            var UIN = $('#UIN').val();
            var current_password = $('#current_password').val();
            var new_password = $('#new_password').val();

            var fd = new FormData();
            fd.append("UIN", UIN);
            fd.append("current_password", current_password);
            fd.append("new_password", new_password);
            fd.append("function", "change password");

            if(confirm("Are you sure?")){
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "_partials/update_info.action.php");
                xhr.send(fd);

                xhr.onload = function () {
                    var data = this.responseText;
                    console.log(JSON.parse(data));
                }
            }
        })

        $('#fix_broken_hours_form').submit(function (e) {
            e.preventDefault();

            if($('#fix_broken_hours_form').valid() && confirm("Are you sure?")){
                var UIN = $('#hour_UIN').val();
                var date = $('#date').val();
                var time_in = $('#time_in').val();
                var time_out = $('#time_out').val();

                var datetime_in = date + " " + time_in;
                var datetime_out = date + " " + time_out;

                console.log(datetime_in, datetime_out);

                fd = new FormData();
                fd.append('in', datetime_in);
                fd.append('out', datetime_out);
                fd.append('UIN', UIN);
                fd.append('function', 'fix_log');

                xhr = new XMLHttpRequest();

                xhr.open('POST', '_partials/clock_action.php');
                xhr.send(fd);

                xhr.onload = function () {
                    var data = JSON.parse(this.responseText);
                    console.log(data);

                    if(data['errorCode'] === 0){
                        $('#msg-target').html("<div class='alert alert-success'><p><strong>Success:</strong> " +
                            "The log has been corrected.</p></div>")
                            .fadeIn(1000).delay(3000).fadeOut();
                    } else {
                        $('#msg-target').html("<div class='alert alert-danger'><p><strong>Error:</strong> " +
                            "The log was not able to be corrected. Please see Tyler.</p></div>")
                            .fadeIn(1000).delay(3000).fadeOut();
                    }
                }

            }
        })

        $('#bypass_broken_hours').submit(function (e) {
            e.preventDefault();

            if($('#bypass_broken_hours').valid() && confirm("Are you sure?")){
                var UIN = $('#broken_hour_UIN').val();
                var date = $('#broken_date').val();
                var time_in = $('#broken_time_in').val();
                var time_out = $('#broken_time_out').val();

                var datetime_in = date + " " + time_in;
                var datetime_out = date + " " + time_out;

                console.log(datetime_in, datetime_out);

                fd = new FormData();
                fd.append('in', datetime_in);
                fd.append('out', datetime_out);
                fd.append('UIN', UIN);

                xhr = new XMLHttpRequest();

                xhr.open('POST', '_partials/add_time.php');
                xhr.send(fd);

                xhr.onload = function () {
                    var data = JSON.parse(this.responseText);
                    console.log(data);

                    if(data['errorCode'] === 0){
                        $('#msg-target').html("<div class='alert alert-success'><p><strong>Success:</strong> " +
                            "The log has been corrected.</p></div>")
                            .fadeIn(1000).delay(3000).fadeOut();
                    } else {
                        $('#msg-target').html("<div class='alert alert-danger'><p><strong>Error:</strong> " +
                            "The log was not able to be corrected. Please see Tyler.</p></div>")
                            .fadeIn(1000).delay(3000).fadeOut();
                    }
                }

            }
        })
    })
</script>

<link rel="stylesheet" href="datetimepicker-master/build/jquery.datetimepicker.min.css">
<script src="datetimepicker-master/build/jquery.datetimepicker.full.min.js"></script>

</body>
</html>