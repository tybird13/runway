
<?php require_once "../_partials/imports.php"?>

</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <form id="addJobForm" method="post">

                <div class="form-group">
                    <label class="control-label">Your First & Last Name<i
                            class="fa fa-asterisk required"></i></label>
                    <input class="form-control" type="text" name="jfname" id="jfname"/>
                </div>

                <div class="form-group">
                    <label class="control-label">Your Company Name</label>
                    <input class="form-control" type="text" name="jcname" id="jcname"/>
                </div>

                <div class="form-group">
                    <label class="control-label">The Property Street Address<i
                            class="fa fa-asterisk required"></i></label>
                    <input class="form-control" name="jaddress" id="jaddress"/>
                </div>

                <div class="form-group">
                    <label class="control-label">The Property City<i
                            class="fa fa-asterisk required"></i></label>
                    <input class="form-control" name="jcity" id="jcity"/>
                </div>

                <div class="form-group">
                    <label class="control-label">The Property State<i
                            class="fa fa-asterisk required"></i></label>
                    <input class="form-control" name="jstate" id="jstate"/>
                </div>

                <div class="form-group">
                    <label class="control-label">The Property Zip Code<i
                            class="fa fa-asterisk required"></i></label>
                    <input class="form-control" name="jzip" id="jzip"/>
                </div>

                <div class="form-group">
                    <label class="control-label">The Property Size<i
                            class="fa fa-asterisk required"></i></label>
                    <select class="form-control" name="jsize" id="jsize">
                        <option value="0">< 3000 sqft</option>
                        <option value="1">3000 - 4000 sqft</option>
                        <option value="2"> 4000 - 5000 sqft</option>
                        <option value="3"> 5000 - 6000 sqft</option>
                        <option value="4"> 6000 - 7000 sqft</option>
                        <option value="5"> 7000 - 8000 sqft</option>
                        <option value="6"> 8000 - 9000 sqft</option>
                        <option value="7"> 9000 - 10,000 sqft</option>
                        <option value="8"> > 10,000 sqft</option>

                    </select>
                </div>


                <div class="form-group">
                    <label for="jobdate" class="control-label">The Requested Date<i
                            class="fa fa-asterisk required"></i></label>
                    <input class="form-control" name="date" id="jobdate">
                    <p class="text-info">We recommend the morning for east facing houses, and the afternoon
                        for west facing houses.</p>
                </div>

                <div class="form-group">
                    <label class="control-label">Your Email Address<i
                            class="fa fa-asterisk required"></i></label>
                    <input class="form-control" type="email" name="jemail" id="jemail"/>
                </div>

                <div class="form-group">
                    <label class="control-label">Your Phone Number<i
                            class="fa fa-asterisk required"></i></label>
                    <input class="form-control" name="jphone" id="jphone"/>
                </div>

                <div class="form-group">
                    <label for="jreferral" class="control-label">Referral's Full Name</label>
                    <input class="form-control" name="jreferral" id="jreferral"/>
                    <p class="text-info">If you were referred to us, please enter their name so we can thank them!</p>
                </div>


                <div class="form-group">
                    <div class="control-label">
                        <label>Services Requested:<i class="fa fa-asterisk required"></i> </label>
                    </div>
                    <div class="checkbox">
                        <label><input id="Drone" name="Drone" type="checkbox"/>Drone</label>
                        <label><input id="DSLR" name="DSLR" type="checkbox"/>Interior</label>
                        <label><input id="vdusk" name="vdusk" type="checkbox">Virtual Twilight</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 text-right">
                        <div class="checkbox">
                            <label><input id="terms" name="terms" type="checkbox">I agree to the
                                <a target="_blank" href="../_text-documents/Contract_booking.pdf">Terms & Conditions</a></label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-5">
                        <h1 id="total">Total: <span class="text-center" id="cost">$0.00</span></h1>
                    </div>
                    <div class="col-xs-7" align="right">
                        <button id="addJobSubmit" class="btn btn-primary" name="token" value="1" type="submit">
                            Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    var cost = 0;
    $('#vdusk').parent().hide();

    $('#jsize, #Drone, #DSLR, #vdusk').on('change', function () {
        console.log("TEST BITCHY BITCH");

        var drone = $('#Drone').prop('checked');
        var dslr = $('#DSLR').prop('checked');
        var jsize = parseInt($('#jsize').val());
        var vdusk = $('#vdusk').prop('checked');

        cost = 0;

        // check to see if it is just the drone
        if(jsize < 8){

            if(!drone && !dslr){
                $('#total').html("Total: <span class=\"text-center\" id=\"cost\">$0.00</span>");
                $('#vdusk').parent().hide();

            } else {
                $('#vdusk').parent().show();
                if(drone && !dslr){
                    cost += 150;
                    if(vdusk){
                        cost += 100;
                    }
                } else {
                    // either dslr and drone, or just dslr
                    if(drone){
                        cost += 100;
                    }

                    cost += 150 + 100 * jsize;

                    if(vdusk){
                        cost += 100;
                    }
                }

                $('#cost').text("$" + cost + ".00");
            }


        } else {
            $('#total').text("Please Call Us");
        }


    });
</script>
</body>
</html>

