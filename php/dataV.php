<?php


//return top 10 winrate champs.
if($_POST['action'] === "top-winrate all")  {
	error_reporting(0);
	$html = file_get_contents('http://www.leagueofgraphs.com/champions/stats/by-winrate');


	$doc = new DOMDocument();
    $doc->loadHTML($html);
    $spans = $doc->getElementsByTagName('span');

    $ret = array();
    $i = 0;
    $x = 0;
    $name = "";
    $rate = 0;
    $curr = true;

	foreach ($spans as $span) {
		//if($i >= 10)
		//	break;

    	if ($span->getAttribute('class') === 'name') {
			
			$name = trim($span->nodeValue);
			//array_push($ret, $name);
			$curr = false;
			
		}	
		if ($span->getAttribute('class') === 'percentage' && substr($span->nodeValue,0,2)>40) {
			if($curr === false) {
				$arr = array("name"=>$name, "size"=>substr($span->nodeValue,0,4));
				array_push($ret, $arr);
				$curr = true;
				$i++;
			}

			$x++;
		}

	}




	echo json_encode($ret);
	//echo $name;

}



if($_POST['action']==="top-popularity") {
	error_reporting(0);
	$html = file_get_contents('http://www.leagueofgraphs.com/champions/stats');


	$doc = new DOMDocument();
    $doc->loadHTML($html);
    $spans = $doc->getElementsByTagName('span');

    $ret = array();
    $i = 0;
    $x = 0;
    $curr = true;
    foreach($spans as $span) {
    	if($i>=130)
    		break;
    	if ($span->getAttribute('class') === 'name') {
			
			$name = trim($span->nodeValue);
			//echo $name;
			//array_push($ret, $name);
			$curr = false;
			
		}	
		if ($span->getAttribute('class') === 'percentage' && substr($span->nodeValue,0,2)<50) {
			if($curr === false) {
				$arr = array("name"=>$name, "size"=>substr($span->nodeValue,0,4));
				//secho $arr["name"];
				array_push($ret, $arr);
				$curr = true;
				$i++;
			}

			$x++;
		}

    }

     $final = array("name"=>"flare", "children"=>$ret);
     echo json_encode($final);
}
//foreach($html->find('span.name') as $element) 
  //ss    echo $element;