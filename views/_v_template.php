<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DWA E-15: Project 2 - Squirrel Cage mini-blog exercise">
    <meta name="author" content="Jeff Linson">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">

    <title><?php if(isset($title)) echo $title; ?></title>

    <!-- Common CSS/JS -->\
  <?php if(IN_PRODUCTION): ?>
    <link rel="stylesheet" href="/css/bootstrap.min.css" type="text/css">
  <?php else: ?>
    <link rel="stylesheet" href="/css/bootstrap.css" type="text/css">
  <?php endif; ?>

    <!-- Controller Specific CSS/JS -->
    <?php if(isset($client_files_head)) echo $client_files_head; ?>

</head>

<body>	

    <?php if(isset($content)) echo $content; ?>

    <!-- Common JS -->
  <?php if(IN_PRODUCTION): ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
  <?php else: ?>
    <script src="/js/jquery-1.10.2.js"></script>
  <?php endif; ?>

    <!-- Controller Specific JS -->
    <?php if(isset($client_files_body)) echo $client_files_body; ?>
</body>
</html>