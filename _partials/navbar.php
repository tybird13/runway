<?php


?>

<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- BRAND AND TOGGLE -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/index.php"><img src="/img/logo.png" class="navbar-logo"></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <!-- LINKS GO HERE -->
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <?php
                    if(isset($_SESSION['username'])):
                ?>
                <li><a>Welcome, <?php echo $_SESSION['username'];?></a></li>

                <?php
                    endif;
                ?>

            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>