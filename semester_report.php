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

if(isset($_SESSION['is_admin'])){
    if(!$_SESSION['is_admin']){
        header("Location: index.php");
    }
} else {
    header("Location: index.php");
}

$config = json_decode(file_get_contents('_partials/config.conf'), true);

$DM = new DatabaseManager();

$all_records = $DM->pullAllFromDatabase("SELECT * FROM users");


//var_dump($all_records);
?>

<script src="scripts/table-sort.js"></script>
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

            <h1 class="text-center">Semester <?php echo $config['semester']?> Report</h1>
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <th>UIN</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Total Hours</th>
                        <th>Logged In Currently?</th>
                        <th>Last Clock In</th>
                        <th>Last Clock Out</th>

                    </tr>

                    <?php foreach ($all_records as $student): ?>
                        <tr>
                            <td><a href="student_report.php?UIN=<?php echo $student['UIN'] ?>"><?php echo $student['UIN'] ?></a></td>
                            <td><?php echo $student['fname'] ?></td>
                            <td><?php echo $student['lname'] ?></td>
                            <td><?php echo $student['eagle_mail'] ?></td>
                            <td><?php echo sprintf("%.3f", $student['total_hours']) ?></td>
                            <td><?php echo $student['clocked_in'] ? "YES" : "NO" ?></td>
                            <td><?php echo date_create($student['last_clock_in'], new DateTimeZone("America/New_York"))->format("D, M dS Y h:iA") ?></td>
                            <td><?php echo date_create($student['last_clock_out'], new DateTimeZone("America/New_York"))->format("D, M dS Y h:iA") ?></td>

                        </tr>

                    <?php endforeach; ?>

                </table>
            </div>

        </div>

    </div>

</div>


</body>
</html>