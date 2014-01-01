<!DOCTYPE html>
<html>
  <head>
    <title>Code Generator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/s.css" rel="stylesheet">
	<script src="https://code.jquery.com/jquery.js"></script>
    <script src="jquery.elastic.source.js"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <script>
    $(function() {
        $('textarea').elastic();
    });
  </script>
    
        <header class="navbar navbar-inverse navbar-fixed-top" role="navigation">
         <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="/codeGen/">Code generator</a>
            </div>
             <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                  <li class="active"><a href="#">Flat</a></li>
                  <li><a href="#">Layer</a></li>
                  <li><a href="#">Instream</a></li>
                  <li><a href="#">Cookie</a></li>
                </ul>
            </div>
            </div>
        </header>
    <div class="container">
<?php
include('namespace.php');
if(!empty($_POST)) {
    @$images = trim($_POST['creativesInput']);
    @$urls = $_POST['urlsInput'];
    @$capping = $_POST['cappingInput'];
}

if(!empty($_POST)) {
    $imagesAr = explode("\n", $images);
    $urlsAr = explode("\n", $urls);

    $i = 0;
    
	foreach($imagesAr as $img) {
		$img = trim($img);
		@$temp_url = trim($urlsAr[$i]);
		
		if($temp_url != '') 
			@$url = $temp_url;
			
		$adv[$i] = new Adv($img, $url);
        $i++;
    }
    
}
 ?>
         <div class="row">
            <div class="col-md-3">
                <div class="bs-sidebar hidden-print affix" role="complementary">
<?php
if(!empty($adv)) {
    $total = count($adv);
    echo '
        <ul class="nav bs-sidenav">
    ';
    for($i = 0; $i < $total; $i++) {
            $name = $adv[$i]->getName();
            $id = $adv[$i]->getID();
            echo '
                <li><a href="#'.$id.'">'.$name.'</a></li>
            ';
    }
    echo '
        </ul>
    ';
}
?>
                </div>
            </div>
            <div class="col-md-9">
                <div id="creativeForm">
                    <form role="form" method="post">
                        <div class="form-group">
                            <label for="creativesInput">Creatives:</label>
                            <textarea class="form-control" name="creativesInput" id="creativesInput" placeholder="Enter URLs of the creatives"><?php if(!empty($images)) echo $images;?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="urlsInput">URLs:</label>
                            <textarea class="form-control" name="urlsInput" id="urlsInput" placeholder="Enter URLs"><?php if(!empty($urls)) echo $urls;?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="cappingInput">OAS capping:</label>
                            <textarea class="form-control" name="cappingInput" id="cappingInput" placeholder="Enter cookie capping used by OAS"><?php if(!empty($capping)) echo $capping;?></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="reset" class="btn btn-danger">Reset</button>
                     </form>
                 </div>
            
       
    
<?php


if(!empty($adv)) {
?>
               <div id="generatedCode">
<?php
    $total = count($adv);
    for ($i = 0; $i < $total; $i++) {
        $multiAdv = $adv[$i]->multipleAdvs();
        $name = $adv[$i]->getName();
        $id = $adv[$i]->getID();
        echo '
        <div id="'.$id.'" class="singleAdv">
        ';
        echo '
            <div class="form-group">
                <label>Creative name</label>
                <input type="text" class="form-control" value="'.$name.'">
            </div>            
        ';
        $multi = $adv[$i]->multipleAdvs();
        if($multi > 1) {
            for ($j = 0; $j < $multi; $j++) {
                echo '
                    <button type="button" class="btn btn-default">Code '.($j+1).'</button>
                    ';
           }    
        }
        $j = 0;
        echo '<div class="codetxt">';
        foreach($adv[$i]->getCode() as $arr) {
            
            echo '
                <div class="form-group" id="'.$id.$j.'">
                    <label>Code</label>
                    <textarea class="form-control">'.$arr['code'];
            if(!empty($capping)) echo(trim($capping));
            echo '
                    </textarea>
                </div>
            ';
            $j++;
        }
        echo '
            </div>
        </div>
        ';
    }
?>
                </div>
<?php
}

?>
           
        </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>

