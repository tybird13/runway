<script>
    $(function () {


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
                fd.append('UIN', '<?php echo $_SESSION['UIN'] ?>');


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
                        $('.msg-target').html('<h2 class="alert alert-danger">Time must be after your last clock in!</h2>').fadeIn('slow');
                        $('.msg-target').delay(2000).fadeOut('slow');

                }
            }

        });

        $('#clock_in').click(function () {

            if(confirm("Are you sure you would like to clock in?")){
                var xhr = new XMLHttpRequest();
                var fd = new FormData();

                fd.append('UIN', '<?php echo $_SESSION['UIN'] ?>');
                fd.append('function', 'clock_in');

                xhr.open('post', '_partials/clock_action.php');
                xhr.send(fd);

                xhr.onload = function(){
                    //console.log(this.responseText);
                    var data = JSON.parse(this.responseText);

                    if(data['errorCode'] === 2) {
                        // THIS MEANS THAT THE USER FORGOT TO LOG OUT PREVIOUSLY

                        // hide the log in button, and replace with an input asking for the time the user
                        // left on the specified date.
                        $('#clock_in').hide();
                        $('#clock_out').hide();
                        $('#date_missed').text(data['date_missed']);
                        $('#time_input_section').removeClass('hide').hide().fadeIn('slow');

                    } else if(data['errorCode'] === 6){
                        $('.msg-target').html('<h2 class="alert alert-danger">You are already clocked in!</h2>').fadeIn('slow').delay(2000).fadeOut();

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

                fd.append('UIN', '<?php echo $_SESSION['UIN'] ?>');
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

                        // hide the log in button, and replace with an input asking for the time the user
                        // left on the specified date.
                        $('#clock_in').hide();
                        $('#clock_out').hide();
                        $('#date_missed').text(data['date_missed']);
                        $('#time_input_section').removeClass('hide').hide().fadeIn();

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