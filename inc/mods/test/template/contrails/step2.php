<?

$l = $this->var_get('list');

echo '<h1>'.e::o('step2_headline').'</h1>';

echo '<p>'.e::o('step2_content').'</p>';

while($l->next())
{
	echo '<div class="navbar"><div class="navbar-inner"><a class="brand" href="'.$OPC->lnk(array(
		'mod'=>'test',
		'event' => 'step2',
		'data[tajapa_currency]' => $l->f('id')
	)).'">'.$l->f('label').'</a></div></div>';
}

?>