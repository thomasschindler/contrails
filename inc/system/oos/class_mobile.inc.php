<?
class MOBILE
{
	function typeable($l=6)
	{
		$c = array('a','d','g','j','m','p','t','w');
		$last = -1;
		for($i=0;$i<$l;$i++)
		{
			do
			{
				$current = rand(0,7);
			}
			while($last == $current);
			$last = $current;
			$r .= $c[$current];
		}
		return $r;
	}
	
	function readable($l=6)
	{
		$c = array('d','g','j','m','p','t','w');
		$last = -1;
		if(rand(0,1)==1)
		{
			$pre = 'a';
			$post = '';
		}
		else
		{
			$pre = '';
			$post = 'a';
		}
		for($i=0;$i<$l;$i++)
		{
			do
			{
				$current = rand(0,7);
			}
			while($last == $current);
			$last = $current;
			$r .= $pre.$c[$current].$post;
		}
		return substr($r,0,$l);
	}
}
?>