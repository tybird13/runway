<?php
/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 7/10/2017
 * Time: 1:27 PM
 */
require_once '_partials/cookie.php';
require_once '_partials/imports.php';
?>

</head>
<body>
<div>
    <h1 class="title">
        <?php
            if(isset($_SESSION['fname'])){
               header("Location: dashboard.php");
            } else {
                header("Location: login.php");
            }

        ?>
        ?>
    </h1>
</div>
</body>
</html>
