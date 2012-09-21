<?$OPC->lang_page_start('page')?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>contrails</title>
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
      <div class="row">
        <div class="span12">
  

        	<h1>Create a new page</h1>
        	<p>

				<?  
				if ($msg = trim($OPC->get_var('page', 'msg'))) 
				{
					echo '<span class="label label-success">'.$msg.'</span>';
				} 

				$vid  = $OPC->get_var('page', 'vid');
				$form = $OPC->get_var('page', 'form');
				$err  = $OPC->get_var('page', 'error');

				// kommen wir von 'seite-bearbeiten'-seite
				$edit_pid = (int)UTIL::get_post('edit_pid');
				//if ($edit_pid) $form->add_hidden('edit_pid', $edit_pid);
				
				if($err) 
				{
					echo '<span class="label label-important">'.e::o('pa_enter_err_fields').'</span>';	
				}

				$form->add_button('event_pa_save', e::o('save'));

				$form->add_hidden('mod',  'page');
				$form->add_hidden('vid',  $vid);
					
				echo $form->start();
				echo $form->fields();
				echo $form->end();
				?>

			</p>
        </div>
      </div>
    </div>
  </body>
</html>

<?$OPC->lang_page_end()?>
