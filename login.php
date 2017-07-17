<?php
require_once '_partials/imports.php';

?>

<title>Log In</title>
<script src="scripts/form_validation.js"></script>
</head>
<body>
<?php require_once '_partials/navbar.php';?>

<div class="container">
    <div class="row">
        <div class="col-sm-offset-2 col-sm-8">
            <div class="sign-in-box">
                <h1>Welcome To Runway</h1>
                <hr>
                <div class="error-target">
                    <p class="text-danger">
                        <?php
                        if(isset($_GET['e'])){
                            $msg = "";
                            switch($_GET['e']){
                                case 1:
                                    $msg = "Please enter your UIN and click 'setup'";
                                    break;
                            }
                            echo $msg;
                        }
                        ?>
                    </p>
                </div>
                <form id="login" class="form-horizontal" method="post" action="">
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="UIN">Enter Your UIN <i class="fa fa-asterisk text-danger"></i></label>
                        <div class="col-sm-8">
                            <input type="text" autocomplete="off" name="UIN" class="form-control" id="UIN">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="pass">Enter Your Password <i class="fa fa-asterisk text-danger"></i></label>
                        <div class="col-sm-8">
                            <input autocomplete="off" type="password" name="pass" class="form-control" id="pass">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-9 col-sm-3">
                            <button style="float:right;" id="submit" class="btn btn-success">Log In</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <p align="left">No password? <span id="setup" class="btn btn-default btn-xs">Setup Account</span></p>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){


        $("#submit").click(function (e) {
            e.preventDefault();
            console.log("SUBMIT");

            // HANDLE FORM SUBMISSION
            if($('#login').valid()){

                var UIN = $('#UIN').val();
                var pass = $('#pass').val();


                var xhr = new XMLHttpRequest();
                var formData = new FormData();

                formData.append('UIN', UIN);
                formData.append('pass', pass);

                xhr.open('post', '_partials/log_in_action.php');
                xhr.send(formData);

                xhr.onload = function () {
                    console.log("RETURNED");
                    var data = JSON.parse(this.responseText);

                    if(data['errorCode'] !== 0){
                        $('.error-target').children('p').html(data['errorMsg']);
                    } else {
                        window.location.href = "dashboard.php";
                    }
                }
            }

        });

        $("#setup").click(function (e) {
            e.preventDefault();

            var UIN = $('#UIN').val();
            //console.log(UIN);

            window.location.href = "setup_account.php?UIN=" + UIN;
        });
    })
</script>
</body>
