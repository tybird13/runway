<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 7/18/2017
 * Time: 1:52 PM
 */
require_once '_partials/cookie.php';
require_once '_partials/guard.php';
require_once '_partials/imports.php';
require_once '_partials/DatabaseManager.Class.php';
require_once 'scripts/button_functions.php';

if (isset($_SESSION['is_admin'])) {
    if (!$_SESSION['is_admin']) {
        header("Location: index.php");
    }
} else {
    header("Location: index.php");
}

// function to make sure that the clocked time is set, and is not equal to NULL
function check_null ($time)
{

    if ($time != NULL && $time != 'NULL') {
        return DateTime::createFromFormat("Y-m-d H:i:s",
            $time,
            new DateTimeZone("America/New_York"))
            ->format("m/d/Y h:i A");
    } else {
        return "NULL";
    }

}

$config = json_decode(file_get_contents('_partials/config.conf'), true);

$DM = new DatabaseManager();

$all_records = $DM->pullAllFromDatabase("SELECT * FROM users");

/* COMPILE ALL DATA FOR THE CHARTS */

// DAYS OF THE WEEK

/*
 * 0 - sunday
 * 1 - monday
 * 2 - tuesday
 * 3 - wednesday
 * 4 - thursday
 * 5 - friday
 * 6 - saturday
 */
$days = array(0, 0, 0, 0, 0, 0, 0);

// parse every single *.log file in the system for the clock-in date

$files = glob("./log/*.log");
$count = 0;
foreach ($files as $file) {
    try {
        $handle = fopen($file, "r");
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {

            $date = $data[0];
            if ($date != null && $date != '') {
                $count++;
                $dow = date('w', strtotime($date)); // 0 - 6
                $days[$dow]++;
            }
        }
    } catch (Exception $e) {
        $e->getMessage();
    }
}

// STUDENTS' HOURS
$people = array(); // [student name] => hours

foreach ($all_records as $record) {
    $key = "{$record['fname']} {$record['lname']}";
    $people["$key"] = $record['total_hours'];
}

asort($people);
?>

<script src="scripts/table-sort.js"></script>
<script src="scripts/jquery.canvasjs.min.js"></script>
<title>Complete Runway Report</title>
</head>

<body>
<script>$(function () {
        $('#semester_report').addClass('active');
    })</script>

<?php require_once '_partials/navbar.php' ?>
<div class="container">

    <div class="row">
        <div class="col-xs-12">
            <h1 class="text-center">Semester <?php echo $config['semester'] ?> Report</h1>
            <h3 class="text-center"># of Students: <?php echo count($all_records) ?></h3>
            <h3 class="text-center"># Logged In:
                <?php
                $count = 0;
                foreach ($all_records as $student) {
                    if ($student['clocked_in']) {
                        $count++;
                    }
                }
                echo $count;
                ?>
            </h3>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <!--   PUT ONE OF THE CHARTS HERE-->
            <div class="chart" id="daysOfWeekChart" style="width: 100%; height: 500px"></div>
        </div>
        <div class="col-xs-12">
            <!--   PUT ONE OF THE CHARTS HERE-->
            <div class="chart" id="peopleHoursChart" style="width: 100%; height: 750px"></div>
        </div>

    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="table-responsive" style="margin-top:50px">
                <table class="table">
                    <tr>
                        <th>UIN</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Total Hours</th>
                        <th>Logged In Currently?</th>
                        <th>Last Clock In (m/d/Y)</th>
                        <th>Last Clock Out (m/d/Y)</th>

                    </tr>

                    <?php foreach ($all_records as $student): ?>
                        <tr>
                            <td>
                                <a href="student_report.php?UIN=<?php echo $student['UIN'] ?>"><?php echo $student['UIN'] ?></a>
                            </td>
                            <td><?php echo $student['fname'] ?></td>
                            <td><?php echo $student['lname'] ?></td>
                            <td><?php echo $student['eagle_mail'] ?></td>
                            <td><?php echo sprintf("%.3f", $student['total_hours']) ?></td>
                            <td><?php echo $student['clocked_in'] ? "YES" : "NO" ?></td>
                            <td>
                                <?php echo check_null($student['last_clock_in']) ?>
                            </td>
                            <td>
                                <?php echo check_null($student['last_clock_out']) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </table>
            </div>

        </div>

    </div>

</div>
<script type="text/javascript">
    window.onload = function () {
        var chart1 = new CanvasJS.Chart("daysOfWeekChart", {
            animationEnabled: true,
            axisY:{
              title: "# of Logins"
            },
            title:{
                text: "Days of the Week"
            },
            data: [
                {
                    type: "column",
                    dataPoints: [
                        {label: "Sunday", y: <?php echo $days[0] ?>  },
                        {label: "Monday", y: <?php echo $days[1] ?> },
                        {label: "Tuesday", y: <?php echo $days[2] ?>  },
                        {label: "Wednesday", y: <?php echo $days[3] ?>  },
                        {label: "Thursday", y: <?php echo $days[4] ?>  },
                        {label: "Friday", y: <?php echo $days[5] ?>  },
                        {label: "Saturday", y: <?php echo $days[6] ?>  }

                    ]

                }
            ]
        });

        chart1.render();

        var chart2 = new CanvasJS.Chart("peopleHoursChart", {
            animationEnabled: true,
            axisY:{
                title: "Hours",
            },
            axisX: {
                labelFontSize: 8,
                interval: 1,
            },
            title:{
                text: "Student Hours"
            },
            data: [
                {
                    type: "bar",
                    dataPoints: [

                        <?php
                        foreach($people as $key => $value){
                            echo "{label: \"$key\", y: $value},\n";
                        }
                        ?>

                    ]

                }
            ]
        });

        chart2.render();

    }
</script>

</body>
</html>