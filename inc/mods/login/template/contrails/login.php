<?$OPC->lang_page_start('page')?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>login</title>
    <link href="/template/contrails/bootstrap/css/bootstrap.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/template/contrails/bootstrap/css/bootstrap-responsive.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/template/contrails/contrails/css/contrails.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/template/contrails/colorbox/colorbox.css" media="screen" rel="stylesheet" type="text/css" />
    
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
    <script src="/template/contrails/colorbox/jquery.colorbox-min.js"></script>
    <script src="/template/contrails/contrails/js/contrails.js"></script>
    
  </head>
  <body>
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
        <div class="span12">
        	<?=$OPC->var_get('content')?>
        </div>
      </div>
    </div>
  </body>
</html>

<?$OPC->lang_page_end()?>
