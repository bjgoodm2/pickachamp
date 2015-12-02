<?php
	include('simple_html_dom.php');

	$champName = $_GET['champName'];

	$html = file_get_html('http://champion.gg/champion/' . $champName);
	$array = array();
	foreach($html->find('.build-wrapper') as $element){
		$x = 0;
		foreach($element->find('img') as $imgs){
			$replaced = $imgs->src;
			$replaced = substr_replace($replaced, 'http://', 0, 2);
			//echo $replaced . '<br>';
			array_push($array, $replaced);
			$x++;
		}
		if ($x === 6){
			break;
		}
	}
	$item_url = 'http://ddragon.leagueoflegends.com/cdn/5.23.1/img/item/';
	//print_r($array);
	echo json_encode($array,JSON_UNESCAPED_SLASHES);
?>
