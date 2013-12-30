<!DOCTYPE html>
<html>
  <head>
    <title>Code Generator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
        <h1>Hello, world!</h1>
        
    
    
<?php
include('namespace.php');

$adv = new Adv('http://vda.wp.pl/RM/Box/2013-12/_mc/bonprix-59908/2013-12-19/316x111_(do15kb).jpg', 'http://www.wp.pl');
$adv->showAll();
if($adv->multipleAdvs()) 
    echo 'many types';
else 
    echo 'single adv';
?>
    
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>

