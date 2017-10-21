<script>
    $(function () {

        $('#UIN-search').submit(function (e) {
            e.preventDefault();
            //console.log("CLICKED");
            var UIN = $('#search').val();
            console.log(UIN);

            window.location.href = "student_report.php?UIN=" + UIN;


        });

        /*
        THIS FUNCTION IS DEPRECATED AS OF 9/2/2017.
        STUDENTS WILL NO LONGER BE ABLE TO FIX THEIR OWN HOURS. INSTEAD, THEY WILL REPORT TO THE ADMINISTRATION AND
        THEY WILL FIX THEIR HOURS WITH THE HELP OF A SEPARATE SCRIPT.

        $('#time-form').submit(function(e){
            e.preventDefault();

            if($('#time-form').valid()){
                // take the time the user types and input it into the database as a clock-out
                // when the user clicks clock-out

                var time = $('#time').val();
                //console.log(time);

                var date = $('#date_missed').text();
                //console.log(date);

                var DateTime = date + " " + time;
                console.log(DateTime);

                var xhr = new XMLHttpRequest();
                var fd = new FormData();

                fd.append('function', 'fix_log');
                fd.append('date_missed', DateTime);
                fd.append('UIN', '< ?php //echo isset($_SESSION['UIN']) ? $_SESSION['UIN'] : NULL ?>');


                // update the user database with the new clock out info
                xhr.open('post', '_partials/clock_action.php');
                xhr.send(fd);

                xhr.onload = function () {
                    console.log("DATA RETURNED");

                    var data = JSON.parse(this.responseText);
                    console.log(data);

                    if(data['errorCode'] === 0){

                        //alert(data['number of hours']);

                        $('.msg-target').html('<h2 class="alert alert-success">You have clocked out successfully!</h2>').fadeIn('slow');
                        $('.msg-target').delay(2000).fadeOut('slow', function () {
                            location.reload(false);
                        });

                    } else if(data['errorCode'] === 4)
                        $('.msg-target').html('<h2 class="alert alert-danger">'+ data['errorMsg'] +'</h2>').fadeIn
                        ('slow');
                        $('.msg-target').delay(2000).fadeOut('slow');

                }
            }

        });
        */

        $('#clock_in').click(function () {

            if(confirm("Are you sure you would like to clock in?")){
                var xhr = new XMLHttpRequest();
                var fd = new FormData();

                fd.append('UIN', '<?php echo isset($_SESSION['UIN']) ? $_SESSION['UIN'] : NULL ?>');
                fd.append('function', 'clock_in');

                xhr.open('post', '_partials/clock_action.php');
                xhr.send(fd);

                xhr.onload = function(){
                    //console.log(this.responseText);
                    var data = JSON.parse(this.responseText);

                    if(data['errorCode'] === 2) {
                        // THIS MEANS THAT THE USER FORGOT TO LOG OUT PREVIOUSLY

                        /*
                        AS OF 9/2/2017
                        WE HAVE DECIDED THAT THE STUDENTS WILL NO LONGER FIX THEIR OWN TIME. THEY
                        WILL COME TO THE ADMINISTRATION, AND THE ADMINS WILL FIX THEIR TIME.

                        THE FIX PUNCH FUNCTION WILL BE HANDLED BY THE ADMINS AND ONLY THE CLOCK IN AND CLOCK OUT
                        FUNCTIONS WILL BE USED BY THE STUDENTS.
                         */

                    } else if(data['errorCode'] === 6){
                        $('.msg-target').html('<h2 class="alert alert-danger">You are already clocked in as of ' +
                            data["date_missed"] + '!</h2>')
                            .fadeIn('slow').delay(5000).fadeOut();

                    } else if(data['errorCode'] === 0){
                        $('.msg-target').html('<h2 class="alert alert-success">You have clocked in successfully!</h2>').fadeIn('slow');
                        $('.msg-target').delay(2000).fadeOut('slow', function () {
                            location.reload(false);
                        });

                    }
                }
            }

        })

        $('#clock_out').click(function(){
            if(confirm("Are you sure you want to clock out?")){
                var xhr = new XMLHttpRequest();
                var fd = new FormData();

                fd.append('UIN', '<?php echo isset($_SESSION['UIN']) ? $_SESSION['UIN'] : NULL ?>');
                fd.append('function', 'clock_out');
                //fd.append()

                xhr.open('post', '_partials/clock_action.php');
                xhr.send(fd);

                xhr.onload = function () {
                    var data = JSON.parse(this.responseText);

                    if(data['errorCode'] === 0){

                        //alert(data['number of hours']);
                        $('.msg-target').html('<h2 class="alert alert-success">You have clocked out successfully!</h2>').fadeIn();
                        $('.msg-target').delay(2000).fadeOut('slow', function () {
                            location.reload(false);
                        });
                    } else if(data['errorCode'] === 5){
                        $('.msg-target').html('<h2 class="alert alert-danger">You are already clocked out!</h2>').fadeIn().delay(2000).fadeOut();

                    } else if(data['errorCode'] === 2){
                        // THIS MEANS THAT THE USER FORGOT TO LOG OUT PREVIOUSLY

                        /*
                        AS OF 9/2/2017
                        WE HAVE DECIDED THAT THE ADMINISTRATION WILL HANDLE THE CORRECTION OF HOURS. STUDENTS WILL NO
                        LONGER BE ABLE TO FIX THEIR OWN HOURS. AN ALERT WILL NOW DISPLAY THAT THEIR TIME PUNCH IS NO
                        LONGER VALID, AND THAT IT NEEDS TO BE CHANGED.
                         */

                        $('.msg-target').html('<div class="alert alert-danger"><h1><strong>ATTENTION:</strong> You ' +
                            'have ' +
                            'been logged in for more than 12 hours! Your time for this punch has been flagged. ' +
                            'You must record the following information and bring it to Tyler or Scott in order to ' +
                            'receive credit for this time.</h1><h2>' + data['errorMsg'] + '</h2></div>').fadeIn();
                        $()

                    }
                }

            }
        })

        $('#cancel').click(function () {
            $('#clock_in').show();
            $('#clock_out').show();
            // $('#date_missed').text(data['date_missed']);
            $('#time_input_section').hide();
        })

    })

</script>