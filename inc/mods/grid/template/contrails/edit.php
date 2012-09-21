<?
	$OPC->lnk_add('data[sx]','500');
	$OPC->lnk_add('data[sy]','500');
	$OPC->lnk_add('data[px]','400');
	$OPC->lnk_add('data[py]','400');

	echo $this->var_get("err");
	
	$mods = $OPC->get_var("grid","mods");
	
	$js_data = array();
	$js_px = array();
	$js_py = array();
	$js_sx = array();
	$js_sy = array();
	$js_z_index = array();
	
	while($mods->next())
	{
		$js_data[] = '"'.$mods->f('vid').'"';
		$js_px[] = '"'.$mods->f('px').'"';
		$js_py[] = '"'.$mods->f('py').'"';
		$js_sx[] = '"'.$mods->f('sx').'"';
		$js_sy[] = '"'.$mods->f('sy').'"';
		$js_z_index[] = '"'.$mods->f('z_index').'"';
		$width[] = $mods->f('sx')+$mods->f('px'); // width+left
		$height[] = $mods->f('py')+$mods->f('sy');
				
	}
	$mods->reset();
	
	// get the squeezebox width and height
	$max_w = 0;
	foreach($width as $w)
	{
		if($w > $max_w)
		{
			$max_w = $w;
		}
	}

	$max_h = 0;
	foreach($height as $h)
	{
		if($h > $max_h)
		{
			$max_h = $h;
		}
	}
	
	$js_squeezebox['width'] = $max_w;
	$js_squeezebox['height'] = $max_h;

	echo '<div id="grid_squeezebox" style="position:relative;height:'.$max_h.'px;width:'.$max_w.'px;" >&nbsp;</div>';
	
?>

<style><!--
	.box {
		position:absolute;
		border-color: black;
		border-style: dashed;
		border-width: 1px;
		overflow: auto;
	}
	/*
	#box_top
	{
		position:absolute;
		top:0px;
		left:0px;
		width: 99%;
		background: #990000;
		height: 30px;
		border: thin dotted white;
		z-index: 40000;
	}
	#box_top span
	{
		margin: 5px;
	}
	#box_foot
	{
		position:absolute;
		bottom:0px;
		left:0px;
		width: 99%;
		height: 30px;
		border: thin dotted white;
	}
	#box_foot span
	{
		margin: 5px;
	}
	*/
	#box_top_left
	{
		position:absolute;
		top:0px;
		left:0px;
		width:25px;
		background: #990000;
		height: 25px;
		border: thin dotted white;
		z-index: 40000;
	}
	#box_top_right
	{
	
		position:absolute;
		top:0px;
		right:0px;
		width:25px;
		background: #990000;
		height: 25px;
		border: thin dotted white;
		z-index: 40001;
	}

	#box_foot
	{
		position:absolute;
		bottom:0px;
		right:0px;
		width:25px;
		height: 30px;
		background: #990000;
		border: thin dotted white;
	}
	#box_foot span
	{
		margin: 5px;
	}	
	.interface {
		position:absolute;
		top: -140px;
		left: 220px;
		/*
		top: -100px;
		left: 0px;
		width:500px;
		*/
		width: 327px;
		height:60px;
		z-index: 50000;
		padding-left:4px;	
	}
	#oos_grid_head
	{
		background: #990000;
		height: 30px;
		border: thin dotted white;
	}
	#oos_grid_head_left
	{
		float:left;
		position: relative;
		left: 10px;
		top: 7px;
		font-weight: bold;
		color: white;
	}
	#oos_grid_head_right
	{
		float:right;
		position: relative;
		right: 10px;
		top: 7px;
	}
	#oos_grid_head_right span
	{
		position: relative;
		top: -5px;
		color: white;
	}	
	#oos_grid_body
	{
		background: #e0b2b2;
		border:thin dotted #990000;
		border-top: 0;
		height: 60px;
	}
	//-->
</style>
<script language="JavaScript">
	// vars for moving
	//handle to the object in question
	var handle_object = null;
	// type of action -> move or size
	var action_type = null;
	// original position of the object
	var dragx = 0;
	var dragy = 0;
	// sizes
	var sizex = 0;
	var sizey = 0;
	// original position of the mouse
	var posx = 0;
	var posy = 0;
	// grid to snap to
	var gridx = 10;
	var gridy = 10;
	// offset from top and left to adjust grid to the design
	var offsetx = 1;
	var offsety = 2;
	
	var squeezebox_height = 0;
	var squeezebox_width = 0;
	
	var squeezebox;
	
	// object for collecting necessary data
	function te_data() 
	{
		// vars
		this.data = new Array(
			<?
				echo implode(",",$js_data);
			?>
		);
		this.px = new Array(
			<?
				echo implode(",",$js_px);
			?>
		);
		this.py = new Array(
			<?
				echo implode(",",$js_py);
			?>
		);
		this.sx = new Array(
			<?
				echo implode(",",$js_sx);
			?>
		);
		this.sy = new Array(
			<?
				echo implode(",",$js_sy);
			?>
		);
		this.z_index = new Array(
			<?
				echo implode(",",$js_z_index);
			?>
		);
				
		// methods
	    // public
    	this.set_position = te_set_position;
    	this.set_size = te_set_size;
    	this.set_z_index = te_set_z_index;	
	    this.serialize = te_serialize;
	    this.remove_element = te_remove_element;
    	// private
    	this.get_id = te_get_id;
    	this.get_max_height = te_get_max_height;
    	this.get_max_width = te_get_max_width;
    	// set the data in the form
	}
	// remove an element
	function te_remove_element(element)
	{

		id = this.get_id(element);
		document.online.changed.value = 1;
		/* create a new array without the element */
		
		data_tmp = new Array();
		px_tmp = new Array();
		py_tmp = new Array();
		sx_tmp = new Array();
		sy_tmp = new Array();
		z_index_tmp = new Array();
		
		cnt = 0;
		for(i=0;i<this.data.length;i++)
		{
			if(i == id)
			{
				continue;
			}
			data_tmp[cnt] = this.data[i];
			px_tmp[cnt] = this.px[i] + "px";
			py_tmp[cnt] = this.py[i] + "px";
			sx_tmp[cnt] = this.sx[i] + "px";
			sy_tmp[cnt] = this.sy[i] + "px";
			z_index_tmp[cnt] = this.z_index[i];
			
			cnt++;
		}

		this.data = data_tmp;
		this.px = px_tmp;
		this.py = py_tmp;
		this.sx = sx_tmp;
		this.sy = sy_tmp;
		this.z_index = z_index_tmp;	
		
		return true;
	}
	// get the id of an element
	function te_get_id(element)
	{
		for(i=0;i<this.data.length;i++)
		{
			if(this.data[i] == element)
			{
				return i;
			}
		}
		i = this.data.push(element);
		return --i;
	}

	function te_get_max_height()
	{
		max_h = 1;
		tmp_h = 0;
		tmp_e = null;
				
		for(i=0;i<this.data.length;i++)
		{
			tmp_e = document.getElementById(this.data[i]);
			if(tmp_e.style.visibility == "hidden")
			{
				continue;
			}
			tmp_h = tmp_e.style.height.substring(0,tmp_e.style.height.indexOf('px'))*1+tmp_e.style.top.substring(0,tmp_e.style.top.indexOf('px'))*1;
			if(tmp_h >= max_h)
			{
				max_h = tmp_h;
			}
			tmp_h = 0;
		}

		return max_h;
	}
	
	function te_get_max_width()
	{
		var max_h = 1;
		var tmp_h = 0;
		var tmp_e;
		for(i=0;i<this.data.length;i++)
		{
			tmp_e = document.getElementById(this.data[i]);
			if(tmp_e.style.visibility == "hidden")
			{
				continue;
			}
			tmp_h = tmp_e.style.width.substring(0,tmp_e.style.width.indexOf('px'))*1+tmp_e.style.left.substring(0,tmp_e.style.left.indexOf('px'))*1;
			if(tmp_h > max_h)
			{
				max_h = tmp_h;
			}
			tmp_h = 0;
		}
		return max_h;
	}
	
	// set the z-index to the highest for this element
	function te_set_z_index(element)
	{
		// get the current z_index
		var id = this.get_id(element);
		if(!this.z_index[id])
		{
			var my_index = handle_object.style.zIndex * 1;
			this.z_index[id] = my_index;
		}
		else
		{
			var my_index = this.z_index[id];
		}
		// loop through the z_index array
		for(i=0;i<this.z_index.length;i++)
		{
			if(this.z_index[i] > my_index)
			{
				if(i != id)
				{
					var target_index = this.z_index[i];
					var target_element = i;
				}
			}
		}
		// somebody is higher - swap
		if(target_index)
		{
			this.z_index[target_element] = my_index;
			this.z_index[id] = target_index;
			// get the element names
			handle_object.style.zIndex = target_index;
			document.getElementById(this.data[target_element]).style.zIndex = my_index;
		}
	}
	// set a position for an element in the object
	function te_set_position(element,x,y) 
	{
		var id = this.get_id(element);
		this.px[id] = x;
		this.py[id] = y;
	}
	// set the size for an element in the object
	function te_set_size(element,x,y) 
	{
		var id = this.get_id(element);
		this.sx[id] = x;
		this.sy[id] = y;
	}
	// serialize data object
	function te_serialize()
	{
		var ret = "";
		for(i=0;i<this.data.length;i++)
		{
			if(this.data[i])
			{
				ret += "ID[" + this.data[i] + "]" + this.px[i] + ":" + this.py[i]  + ":" + this.sx[i] + ":" + this.sy[i] + ":" + this.z_index[i];
			}
		}
		return ret;
	}
	// initialise data object
	var data = new te_data();
	// initialize
	function te_draginit() 
	{
		document.onmousemove = te_start_action;
		document.onmouseup = te_end_action;
	}
	// start moving an object
	function te_dragstart(element) 
	{
		action_type = 'move';
		handle_object = document.getElementById(element);
		dragx = posx - handle_object.offsetLeft;
		dragy = posy - handle_object.offsetTop;

		squeezebox = document.getElementById("grid_squeezebox");
		
		if(element != "interface")
		{
			data.set_z_index(element);
		}
	}
	// remove the element from the data array and remove the div
	function te_close(element)
	{
		handle_object = document.getElementById(element);
		handle_object.style.visibility = "hidden";
		
		data.remove_element(element);
		squeezebox = document.getElementById("grid_squeezebox");
			
		height = data.get_max_height();
				
		if(height > squeezebox_height)
		{
			squeezebox.style.height = height + "px";
		}
				
		width = data.get_max_width();
		if(width > squeezebox_width)
		{
			squeezebox.style.width = width + "px";
		}
	}
	// change the size
	function te_sizestart(element) 
	{
		if(element != "interface")
		{
			action_type = 'resize';
			handle_object = document.getElementById(element);
			sizex = handle_object.style.width;
			sizey = handle_object.style.height;
			sizex = sizex.substring(0,sizex.indexOf('px'));
			sizey = sizey.substring(0,sizey.indexOf('px'));
			sizex = sizex * 1;
			sizey = sizey * 1;
			dragx = posx;
			dragy = posy;
			data.set_z_index(element);
			
			squeezebox = document.getElementById("grid_squeezebox");
		
		}
	}
	// cleanup
	function te_end_action() 
	{
		if(handle_object.id == "interface"){
			return;
		}
		// do some cleaning up - snap to grid
		if(handle_object != null) {
			if(action_type == 'move')
			{
				if(handle_object.id != "interface")
				{
					dragx = handle_object.style.left;
					dragy = handle_object.style.top;
					dragx = dragx.substring(0,dragx.indexOf('px'));
					dragy = dragy.substring(0,dragy.indexOf('px'));
					dragx = dragx * 1;
					dragy = dragy * 1;
					dragy = (dragy%gridy) <= gridy/2 ? dragy - (dragy%gridy) : dragy + gridy-(dragy%gridy);
					dragx = (dragx%gridx) <= gridx/2 ? dragx - (dragx%gridx) : dragx + gridx-(dragx%gridx);
					handle_object.style.left = ( dragx + offsetx ) + "px";
					handle_object.style.top = ( dragy + offsety ) + "px";
					data.set_position(handle_object.id,handle_object.style.left,handle_object.style.top);
					data.set_size(handle_object.id,handle_object.style.width,handle_object.style.height);
				}
			}
			if(action_type == 'resize')
			{
				if(handle_object.id != "interface")
				{
					sizex = handle_object.style.width;
					sizey = handle_object.style.height;
					sizex = sizex.substring(0,sizex.indexOf('px'));
					sizey = sizey.substring(0,sizey.indexOf('px'));
					sizex = sizex * 1;
					sizey = sizey * 1;
					sizex = (sizex%gridx) <= gridx/2 ? sizex - (sizex%gridx) : sizex + gridx-(sizex%gridx);
					sizey = (sizey%gridy) <= gridy/2 ? sizey - (sizey%gridy) : sizey + gridy-(sizey%gridy);
					handle_object.style.width = sizex + "px";
					handle_object.style.height = sizey + "px";
					data.set_position(handle_object.id,handle_object.style.left,handle_object.style.top);
					data.set_size(handle_object.id,handle_object.style.width,handle_object.style.height);
				}
			}
			if(action_type == 'close')
			{
				// do nothing for now
			}
			if(handle_object.id != "interface")
			{
				document.transport.serialized.value = data.serialize();
				document.online.serialized.value = data.serialize();
			}
		}
		// reset all vars
		handle_object=null;
		dragx = 0;
		dragy = 0;
		sizex = 0;
		sizey = 0;
		posx = 0;
		posy = 0;
		action_type = null;	
	}
	// do something
	function te_start_action(_event) 
	{
		posx = document.all ? window.event.clientX : _event.pageX;
		posy = document.all ? window.event.clientY : _event.pageY;
		if(handle_object.id == "interface"){
			return;
		}
		if(handle_object != null) {
			if(action_type == 'move')
			{
				handle_object.style.left = (posx - dragx) + "px";
				handle_object.style.top = (posy - dragy) + "px";
				
				/*
					we should loop through all objects and find out the target height and width
					and then adapt the squeezebox
				*/
				
				height = data.get_max_height();
				if(height > squeezebox_height)
				{
					squeezebox.style.height = height + "px";
				}
				
				width = data.get_max_width();
				if(width > squeezebox_width)
				{
					squeezebox.style.width = width + "px";
				}
				
			}
			if(action_type == 'resize')
			{
				handle_object.style.height = sizey + ( posy - dragy ) + "px";
				handle_object.style.width = sizex + ( posx - dragx ) + "px";				
				
				height = data.get_max_height();
				if(height > squeezebox_height)
				{
					squeezebox.style.height = height + "px";
				}
				
				width = data.get_max_width();
				if(width > squeezebox_width)
				{
					squeezebox.style.width = width + "px";
				}
				
			}
		}
		// set the value changed in the form to true
		if(handle_object.id != "interface")
		{
			document.online.changed.value = 1;
		}
	}
	te_draginit();
	
	/* copy interface toggle */
	
	function show_copy(id)
	{
		box = document.getElementById(id);
		state = box.style.visibility;
		if(state == "hidden")
		{
			box.style.visibility = "visible";
		}
		else
		{
			box.style.visibility = "hidden";
		}
	}
	
</script>


<div id="gridpanel">
<?=$OPC->get_var('grid','interface')?>
<?=$OPC->get_var('grid','online')?>
</div>

<?

$f = MC::create("form");
$f->init("choose_form",$this->var_get("copy_form_cnf"));
$f->add_button("event_copy_module",e::o("copy",null,null,"grid"));
$f->add_button("event_move_module",e::o("move",null,null,"grid"));
$f->add_hidden("mod","grid");
$f->use_layout("none");

$MC->switch_access(true);

$choose = $this->var_get("choose");

while($mods->next())
{

	$f->add_hidden("data[vid]",$mods->f('vid'));
	$form = $f->start().$f->fields().$f->end();
	
	echo '
		<div id="'.$mods->f('vid').'" class="box" style="left:'.$mods->f('px').'px;top:'.$mods->f('py').'px;width:'.$mods->f('sx').'px;height:'.$mods->f('sy').'px;z-index:'.$mods->f('z_index').';visibility:visible";>
			<div id="copy_'.$mods->f('vid').'" style="border:thin dotted black;background-color:gray;position:absolute;top:28px;visibility:hidden;">
				'.$form.'
			</div>
			<div id="box_top_left" style="width:55px;">
				<span style="background-image: url('.$OPC->show_icon('move_red',true).');float:left;margin-top:2px;margin-left:2px;width:20px;height:20px;" onmousedown="te_dragstart(\''.$mods->f('vid').'\')">&nbsp;</span>
				<img style="padding-left:5px;padding-top:2px;" src="'.$OPC->show_icon('new_page_red',true).'" onClick="show_copy(\'copy_'.$mods->f('vid').'\')" alt="copy" title="copy" />
			</div>
			<div id="box_top_right">
				<span style="background-image: url('.$OPC->show_icon('close_red',true).');float:right;margin-top:2px;margin-right:4px;width:20px;height:20px;" onmousedown="te_close(\''.$mods->f('vid').'\')">&nbsp;</span>
			</div>';
			
			if($choose[$mods->f('vid')])
			{
				echo $choose[$mods->f('vid')];
			}
			else
			{
				$OPC->call_view($mods->f('vid'),$mods->f('mod'),$mods->f('view'),1);
			}
			
			
			echo '
			<div id="box_foot">	
				<span style="background-image: url('.$OPC->show_icon('resize_red',true).');float:right;width:20px;height:20px;" onmousedown="te_sizestart(\''.$mods->f('vid').'\')">&nbsp;</span>
			</div>';
			
		echo '</div>
	';	
}
$MC->switch_access(false);
?>
