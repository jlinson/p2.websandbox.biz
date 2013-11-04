<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DWA E-15: Project 2 - Squirrel Cage mini-blog exercise">
    <meta name="author" content="Jeff Linson">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">

    <title><?php if(isset($title)) echo $title; ?></title>

    <!-- Common CSS/JS -->
  <?php if(IN_PRODUCTION): ?>
    <link rel="stylesheet" href="/css/bootstrap.min.css" type="text/css">
  <?php else: ?>
    <link rel="stylesheet" href="/css/bootstrap.css" type="text/css">
  <?php endif; ?>

    <!-- Controller Specific CSS/JS -->
    <?php if(isset($client_files_head)) echo $client_files_head; ?>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>

<body>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a class="navbar-brand" href="/"><img src="/images/SC-Logo20.png" alt="<?php echo APP_NAME . " logo";?>"</a>
            <a class="navbar-brand" href="/"><?php echo APP_NAME; ?></a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="<?php if($nav_active=='home') echo "active";?>"><a href="/">Home</a></li>
                <li class="<?php if($nav_active=='features') echo "active";?>"><a href="/index/features">Features</a></li>
                <?php if(!$user): ?>
                    <li class="<?php if($nav_active=='signup') echo "active";?>"><a href="/users/signup">Sign-up</a></li>
                    <li class="dropdown, disabled">
                <?php else: ?>
                    <li class="<?php if($nav_active=='profile') echo "active";?>"><a href="/users/profile">Profile</a></li>
                    <li class="dropdown<?php if($nav_active=='dropdown') echo ", active";?>">
                <?php endif; ?>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li class="divider"></li>
                        <li class="dropdown-header">Nav header</li>
                        <li><a href="#">Separated link</a></li>
                        <li><a href="#">One more separated link</a></li>
                    </ul>
                </li>
            </ul>

            <!-- See c_base for login/logout toggle -->
            <?php if(isset($lognav)) echo $lognav; ?>

        </div><!-- /navbar-collapse -->
    </div><!-- /container -->
</div><!-- /navbar- -->

<?php if(isset($content)) echo $content; ?>

    <div class="container">
        <hr>

        <footer>
            <p>&copy; <?php echo COPYRIGHT; ?></p>
        </footer>
    </div> <!-- /container -->

    <!-- Common JS -->
  <?php if(IN_PRODUCTION): ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
  <?php else: ?>
    <script src="/js/jquery-1.10.2.js"></script>
    <script src="/js/bootstrap.js"></script>
  <?php endif; ?>

    <!-- Controller Specific JS -->
    <?php if(isset($client_files_body)) echo $client_files_body; ?>
</body>
</html>