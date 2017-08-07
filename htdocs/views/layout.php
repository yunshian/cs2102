<html>
    <head>
        <title><?=$title ?></title>
        <link href='http://fonts.googleapis.com/css?family=Bitter' rel='stylesheet' type='text/css'>
        <link href='
        https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' rel='stylesheet' type="text/css">
        <link href='/cs2102/css/styles.css' rel='stylesheet' type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        
    </head>
    <body>
        <!-- start navbar -->
        <nav class="navbar navbar-default">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/cs2102/main">SS</a>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-nav-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="header-nav-collapse">
            
        <?php if (!isset($_SESSION)) {
                session_start();
        }?>
        <?php if (isset($_SESSION['userId'])): ?>
                    <ul class="nav navbar-nav navbar-left">
                        <p class="navbar-text"><a href="/cs2102/user/<?= $_SESSION['userId'] ?>"><?= $_SESSION['username'] ?> (id:<?= $_SESSION['userId'] ?>)</a></p>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <?php if ($_SESSION['userType'] === 'admin'): ?>
                        <li><a href="/cs2102/admin">Admin</a></li>
                        <?php endif; ?>
                        <li><a href="/cs2102/createListing">List</a></li>
                        <li><a href="/cs2102/logout">Log out</a></li>
                    </ul>
        <?php else: ?>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="/cs2102/login">Log in</a></li>
                        <li><a href="/cs2102/signup">Sign up</a></li>
                    </ul>
        <?php endif;
              session_commit(); ?>
                </div>
            </div>
        </nav>
        <!-- end navbar -->
        <!-- start contents -->
        <div class="container">
        <?php echo $body_content; ?>
        </div>
        <!-- end contents -->
    </body>
</html>