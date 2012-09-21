#!/usr/bin/php -q
<?

$f = file($argv[1]);

foreach($f as $l)
{

	$l = explode("e::o(",$l);
	if(count($l)>1)
	{
		unset($l[0]);
		foreach($l as $i)
		{
			$i = explode(substr($i,0,1),$i);
			echo "'".$i[1]."' => '',\n";
		}
	}

}

?>