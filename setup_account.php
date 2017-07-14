<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 7/13/2017
 * Time: 3:57 PM
 */
require_once '_partials/imports.php';
require_once '_partials/DatabaseManager.Class.php';

$UIN = !empty($_GET['UIN']) ? $_GET['UIN'] : header("Location: login.php?e=1");
// get the UIN from the user, and check whether they are accepted into the program
$DM = new DatabaseManager();
$result = $DM->accessDatabase("SELECT * FROM accepted_students WHERE UIN=?", array($UIN));
//var_dump($UIN);
?>
<script src="scripts/form_validation.js"></script>
</head>
<body>
<?php require_once '_partials/navbar.php'; ?>
<div class="container">

    <div class="sign-in-box">

        <h1>Please Verify Your Account</h1>
        <h4>UIN: <?php echo $UIN; ?></h4>
        <hr>

        <div class="error-target">
            <p class="text-danger">
                <?php
                    if(isset($_GET['e'])){
                        switch($_GET['e']){
                            case 1:
                                echo "Please enter a password.";
                                break;
                            case 2:
                                echo "UIN Not Specified";
                                break;
                        }
                    }
                ?>
            </p>
        </div>

        <form id="verify" class="form-horizontal" method="post" action="">
            <div class="form-group">
                <label class="col-sm-4 control-label" for="pass">Enter Your First Name <i class="fa fa-asterisk text-danger"></i></label>
                <div class="col-sm-8">
                    <input type="text" autocomplete name="fname" class="form-control" id="fname">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="pass">Enter Your Last Name <i class="fa fa-asterisk text-danger"></i></label>
                <div class="col-sm-8">
                    <input type="text" autocomplete name="lname" class="form-control" id="lname">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="pass">Enter Your School Email <i class="fa fa-asterisk text-danger"></i></label>
                <div class="col-sm-8">
                    <input type="email" autocomplete name="email" class="form-control" id="email">
                </div>
            </div>


            <div class="form-group">
                <div class="col-sm-offset-10 col-sm-2">
                    <button style="float:right;" class="btn btn-success verify-btn">Verify</button>
                </div>
            </div>

        </form>

        <form id="create-password" method="post" action="" class="form-horizontal hide">
            <div class="form-group">
                <label class="col-sm-4 control-label" for="pass">Enter A New Password <i class="fa fa-asterisk text-danger"></i></label>
                <div class="col-sm-8">
                    <input type="password" autocomplete name="new_pass" class="form-control" id="new_pass">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="pass">Re-enter Your Password <i class="fa fa-asterisk text-danger"></i></label>
                <div class="col-sm-8">
                    <input type="password" autocomplete name="retype_pass" class="form-control" id="retype_pass">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-9 col-sm-3">
                    <button id="set-pass" style="float:right;" class="btn btn-success">Set Password</button>
                </div>
            </div>

        </form>
    </div>

</div>

<script type="text/javascript">
    $(function () {
        $('#verify').submit(function (e) {
            e.preventDefault();
        })

        $(".verify-btn").click(function () {
            //console.log("CLICK");
            if($('#verify').valid()){
                $('.error-target').children('p').text('');
                var fname = $('#fname').val();
                var lname = $('#lname').val();
                var email = $('#email').val();
                var UIN = <?php echo $UIN ?> ;

                var xhr = new XMLHttpRequest();
                var fd = new FormData();

                fd.append('function', 'check_info');
                fd.append('fname', fname);
                fd.append('lname', lname);
                fd.append('email', email);
                fd.append('UIN', UIN);

                //console.log("items: " + fname + " " + lname + " " + email);

                xhr.open('post', '_partials/setup_action.php');
                xhr.send(fd);

                xhr.onload = function(){
                    console.log("DATA RETURNED");
                    var data = JSON.parse(this.responseText);
                    console.log(data);

                    if(data['errorCode'] === 0){
                        $('#verify').hide();
                        $('#create-password').show().removeClass('hide');
                    } else {
                        $('.error-target').children('p').text(data['errorMsg']);
                    }

//                    fd = null;
//                    xhr = null;

                }
            }

        })

        $('#create-password').submit(function (e) {
            e.preventDefault();

            if($('#create-password').valid()){
                console.log("SUBMIT");

                var new_pass = $('#new_pass').val();
                var retype_new_pass = $('#retype_pass').val();

                if(new_pass.localeCompare(retype_new_pass) === 0){
                    //console.log("YEEHAH");

                    var xhr = new XMLHttpRequest();
                    var fd = new FormData();

                    fd.append('pass', new_pass);
                    fd.append('function', 'add_password');
                    fd.append('UIN', <?php echo $UIN?>)

                    xhr.open('post', '_partials/setup_action.php');
                    xhr.send(fd);

                    xhr.onload = function () {
                        console.log('DATA RETURNED')
                        var data = JSON.parse(this.responseText);
                        console.log(data);
                    }


                } else {
                    console.log("NOPE");
                }
            }

            return false;
        })
    })


</script>

</body>
</html>