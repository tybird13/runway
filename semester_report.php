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
$days = array(
    array(0, 0, 0), // sunday    (8:00am - 11:59am, 12:00pm - 3:59pm, 4:00pm - 8:00pm)
    array(0, 0, 0), // monday    (8:00am - 11:59am, 12:00pm - 3:59pm, 4:00pm - 8:00pm)
    array(0, 0, 0), // tuesday   (8:00am - 11:59am, 12:00pm - 3:59pm, 4:00pm - 8:00pm)
    array(0, 0, 0), // wednesday (8:00am - 11:59am, 12:00pm - 3:59pm, 4:00pm - 8:00pm)
    array(0, 0, 0), // thursday  (8:00am - 11:59am, 12:00pm - 3:59pm, 4:00pm - 8:00pm)
    array(0, 0, 0), // friday    (8:00am - 11:59am, 12:00pm - 3:59pm, 4:00pm - 8:00pm)
    array(0, 0, 0)  // saturday  (8:00am - 11:59am, 12:00pm - 3:59pm, 4:00pm - 8:00pm)
);

// parse every single *.log file in the system for the clock-in date

$files = glob("./log/*.log");
$count = 0;
foreach ($files as $file) {
    try {
        $handle = fopen($file, "r");
        while (($data = fgetcsv($handle, 1000, ",", ' ', '"')) !== false) {

            $date = preg_replace("/\"/", "", $data[0]);
            
            
            echo $date . "<br>";
            if ($date != null && $date != '') {
                $count++;

                // get the day and time
                $dow = date('w', strtotime($date)); // 0 - 6

                // get the interval that the time falls into
                $fullDate = DateTime::createFromFormat('Y-m-d H:i:s', $date);
                $timeOfDay = DateTime::createFromFormat("H:i", $fullDate->format("H:i"));

                $d1 = DateTime::createFromFormat("H:i", "12:00");
                $d2 = DateTime::createFromFormat("H:i", "16:00");

                if ($timeOfDay < $d1) { // if the time is before noon, it is the morning
                    $days[$dow][0]++;
                } elseif ($timeOfDay >= $d1 && $timeOfDay <= $d2) { // if the time is between noon and 4, it is daytime
                    $days[$dow][1]++;
                } elseif ($timeOfDay > $d2) {
                    $days[$dow][2]++;
                } else {
                    throw new Exception("Time of day was set incorrectly");
                }

                //var_dump($days);
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
            axisY: {
                title: "# of Logins"
            },
            title: {
                text: "Days of the Week"
            },
            data: [
                {
                    type: "stackedColumn",
                    legendText: "Before 12:00PM",
                    showInLegend: true,
                    dataPoints: [
                        {label: "Sunday", y: <?php echo $days[0][0] ?>  },
                        {label: "Monday", y: <?php echo $days[1][0] ?> },
                        {label: "Tuesday", y: <?php echo $days[2][0] ?>  },
                        {label: "Wednesday", y: <?php echo $days[3][0] ?>  },
                        {label: "Thursday", y: <?php echo $days[4][0] ?>  },
                        {label: "Friday", y: <?php echo $days[5][0] ?>  },
                        {label: "Saturday", y: <?php echo $days[6][0] ?>  }

                    ]

                },
                {
                    type: "stackedColumn",
                    legendText: "Between 12:00PM & 4:00PM",
                    showInLegend: true,
                    dataPoints: [
                        {label: "Sunday", y: <?php echo $days[0][1] ?>  },
                        {label: "Monday", y: <?php echo $days[1][1] ?> },
                        {label: "Tuesday", y: <?php echo $days[2][1] ?>  },
                        {label: "Wednesday", y: <?php echo $days[3][1] ?>  },
                        {label: "Thursday", y: <?php echo $days[4][1] ?>  },
                        {label: "Friday", y: <?php echo $days[5][1] ?>  },
                        {label: "Saturday", y: <?php echo $days[6][1] ?>  }

                    ]

                },
                {
                    type: "stackedColumn",
                    legendText: "After 4:00PM",
                    showInLegend: true,
                    indexLabel: "#total LogIns",
                    indexLabelPlacement: "outside",
                    dataPoints: [
                        {label: "Sunday", y: <?php echo $days[0][2] ?>  },
                        {label: "Monday", y: <?php echo $days[1][2] ?> },
                        {label: "Tuesday", y: <?php echo $days[2][2] ?>  },
                        {label: "Wednesday", y: <?php echo $days[3][2] ?>  },
                        {label: "Thursday", y: <?php echo $days[4][2] ?>  },
                        {label: "Friday", y: <?php echo $days[5][2] ?>  },
                        {label: "Saturday", y: <?php echo $days[6][2] ?>  }

                    ]

                }
            ]
        });

        chart1.render();

        var chart2 = new CanvasJS.Chart("peopleHoursChart", {
            animationEnabled: true,
            axisY: {
                title: "Hours",
            },
            axisX: {
                labelFontSize: 8,
                interval: 1,
            },
            title: {
                text: "Student Hours"
            },
            data: [
                {
                    type: "bar",
                    dataPoints: [

                        <?php
                        foreach ($people as $key => $value) {
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