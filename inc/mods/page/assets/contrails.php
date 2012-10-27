<!DOCTYPE html>
<html lang="en">
  <head>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>contrails</title>
    <link href="/template/contrails/bootstrap/css/bootstrap.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/template/contrails/bootstrap/css/bootstrap-responsive.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/template/contrails/contrails/css/contrails.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/template/contrails/colorbox/colorbox.css" media="screen" rel="stylesheet" type="text/css" />
    
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="/template/contrails/bootstrap/js/bootstrap.js"></script>
    <script src="/template/contrails/colorbox/jquery.colorbox-min.js"></script>
    <script src="/template/contrails/contrails/js/contrails.js"></script>

    <link rel="shortcut icon" href="/template/contrails/contrails/img/favicon.ico" />

  </head>
  <body>
    <div id="logo">
      <img src="/template/contrails/contrails/img/logo.png"/>
      <br/>
      contrails
    </div>
    <div class="navbar navbar-static-top">
      <div class="container">
        <?=$OPC->call('page','admin_panel')?>
        <div class="navbar-inner">
          <ul class="nav">
            <li><a href="">About</a></li>
            <li><a href="">More</a></li>
          </ul>
          <ul class="nav pull-right">
            <?=$OPC->call('usradmin','loginlogout')?>
          </ul>
        </div>
      </div>
    </div>
    <div class="container">
      <?
        // display the errors
        $e = $OPC->error();
        $w = $OPC->warning();
        $i = $OPC->information();
        $s = $OPC->success();
        if($e || $w || $i || $s)
        {
          echo '<div class="row"><div class="span12">';
            foreach($e as $msg)
            {
              echo '<div class="alert alert-error fade in"><button type="button" class="close" data-dismiss="alert">×</button>'.$msg.'</div>';
            }
            foreach($w as $msg)
            {
              echo '<div class="alert fade in"><button type="button" class="close" data-dismiss="alert">×</button>'.$msg.'</div>';
            }
            foreach($i as $msg)
            {
              echo '<div class="alert alert-info fade in"><button type="button" class="close" data-dismiss="alert">×</button>'.$msg.'</div>';
            }
            foreach($s as $msg)
            {
              echo '<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert">×</button>'.$msg.'</div>';
            }
          echo '</div></div>';
        }
      ?>
      <div class="row">
        <div class="span10 offsetcontent">
          <?=$OPC->call('test')?>
        </div>
      </div>
    </div>
    <div class="container">
      <footer>© 2012 hotoshi ltd</footer>
    </div>
  </body>
</html>