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
                <?php if(isset($_SESSION['fname'])): // fname is separate just in case I want to add stuff here?>
                    <?php if($_SESSION['is_admin']): ?>
                        <li id="semester_report"><a href="semester_report.php">Semester Report</a></li>
                        <li><a href="edit_account.php">Edit Student Accounts</a></li>
                        <!--search for UIN-->
                        <form id="UIN-search" class="navbar-form navbar-left">
                            <div class="form-group">
                                <label for="search" class="control-label">Search UIN</label>
                                <input id="search" name="search" class="form-control">
                                <button class="btn btn-default btn-sm">Report</button>

                            </div>
                        </form>
                    <?php endif ?>
                <?php endif; ?>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <?php
                    if(isset($_SESSION['fname'])):
                ?>
                <li><a>Welcome, <?php echo $_SESSION['fname'];?></a></li>
                <li><a><span class="btn btn-danger btn-xs" id="logout">Log Out</span></a></li>

                <?php
                    endif;
                ?>

            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
    <script>
        $(function () {
            $('#logout').click(function () {
                window.location.replace("logout.php");
            })
        })
    </script>
</nav>