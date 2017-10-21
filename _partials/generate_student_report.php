<?php function generate_report($student, $UIN) {


    $FILE_PATH = $_SERVER['DOCUMENT_ROOT'] . "/log/" . $UIN . ".log";

    // create an array from the file
    $log_array = array();


    if(!file_exists($FILE_PATH)){
        $log_file = fopen($FILE_PATH, "w+"); // create the file
        fclose($log_file);
        chmod($FILE_PATH, 0664); // set the appropriate permissions
        $log_file = fopen($FILE_PATH, "r");
    } else {

        $log_file = fopen($FILE_PATH, "r"); // just read the file
    }

    if($log_file){
        while(!feof($log_file)){
            array_push($log_array, fgetcsv($log_file));
        }
        fclose($log_file);
    }

    if($log_file){
        ?>

        <h1 class="text-center">Report for <?php echo "{$student['fname']} {$student['lname']} | {$student['UIN']}"?></h1>
        <h2 class="text-center">Total Hours: <?php echo sprintf("%.3f", $student['total_hours'])?></h2>
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>DATE</th>
                    <th>IN</th>
                    <th>OUT</th>
                    <th>HOURS</th>
                </tr>

                <?php
                foreach(array_reverse($log_array) as $line):
                    if($line):
                        if((!empty($line[0]) && !empty($line[1])) &&
                            ($line[0] != "*****" &&
                                $line[1] != "*****")){

                            $date_object = date_create($line[0], new DateTimeZone("America/New_York"));
                            $date = $date_object->format("m/d/Y");

                            $in = date_create($line[0], new DateTimeZone("America/New_York"))->format("h:i A");
                            $out = date_create($line[1], new DateTimeZone("America/New_York"))->format("h:i A");

                            $hours = (strtotime($line[1]) - strtotime($line[0])) / 3600;

                            //echo "{$date} | {$in} -> {$out} | {$hours} \n";
                            ?>
                            <tr>
                                <td><?php echo $date ?></td>
                                <td><?php echo $in ?></td>
                                <td><?php echo $out ?></td>
                                <td><?php echo sprintf("%.2f", $hours)?></td>

                            </tr>

                            <?php
                        }
                    endif;
                endforeach;

                ?>
            </table>
        </div>

        <?php

    } else {
        echo "<h1 class='text-center'>No Hours Logged Yet</h1>";
    }
    ?>


<?php } ?>
