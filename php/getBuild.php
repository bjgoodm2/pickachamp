<?php

	include('simple_html_dom.php');
	
	$champName = $_GET["champName"];

	$html = file_get_html('http://www.probuilds.net/champions/' . $champName);
	$array = array();
	foreach($html->find('.final-builds') as $element){
		$x = 0;
		foreach($element->find('img') as $imgs){
			if ($x === 0){
			   $x++;
			   continue;
			}
			if ($x === 8){
				break;
			}
			array_push($array, $imgs->src);
			//echo $imgs->src . '<br>';
			$x++;
		}
		if ($x === 8){
			break;
		}
	}
	//print_r($array);
	echo json_encode($array, JSON_UNESCAPED_SLASHES);
?>
