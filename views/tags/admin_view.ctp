admin tagging view

<?php

	debug($tag);

	if(isset($tag))	{
		foreach($tag as $item)	{
			echo $html->tag('h4', $tag), $html->tag('p', $item);
			print_r($tag); echo "<hr style='display: block; color: #000; background-color: #000; width: 100%; height: 5px;' />"; print_r($item);
		}
	}
?>