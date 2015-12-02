<?php

if($_POST['action'] == 'match_history') {
	$summoner_name = $_POST["summoner_name"];
	recommend_by_match_history($summoner_name);
} else if ($_POST['action'] == 'role'){
	$primary_role = $_POST["primary_role"];
	$secondary_role = $_POST["secondary_role"];
	recommend_by_role($primary_role, $secondary_role);
} else if ($_POST['action'] == 'match_history_rate') {
	$summoner_name = $_POST["summoner_name"];
	recommend_by_rating($summoner_name);
}
 
/**
 * Recommend by match history
 */
function recommend_by_match_history($summoner_name)
{
    $info = crawl_info($summoner_name);
    $successful_champ = most_successful_champ($info);
    $similar_champs = get_similar_champions($successful_champ);
    $winrates = [];
    foreach ($similar_champs as $similar_champ) {
        $winrates[$similar_champ] = get_winrate_for_champ($similar_champ);
    }
    $best_winrate = 0;
    $best_champ = '';
    foreach ($winrates as $winrate) {
        if ($winrate > $best_winrate) {
            $best_winrate = $winrate;
            $best_champ = array_search($winrate, $winrates);
        }
    }
    echo $best_champ;
}
 
/**
 * Recommend champion by provided primary/secondary roles
 *
 * @param $primary_role string the primary role to search by
 * @param $secondary_role string the secondary role to search by, or null if not applicable
 * @return string the champion that has the highest winrate in the roles provided
 */
function recommend_by_role($primary_role, $secondary_role)
{
    $response = file_get_contents('https://global.api.pvp.net/api/lol/static-data/na/v1.2/champion?champData=tags&api_key=87258093-0098-49d5-8e90-5f0bcad77964');
    $arr = json_decode($response, true);
 
    $champions = [];
    foreach ($arr["data"] as $champion => $champ_data) {
        if ($secondary_role == null) {
            if ($champ_data["tags"][0] == $primary_role && $champ_data["tags"][1] == null) {
                $champions[$champion] = get_winrate_for_champ($champion);
            }
        } else {
            if ($champ_data["tags"][0] == $primary_role && $champ_data["tags"][1] == $secondary_role) {
                $champions[$champion] = get_winrate_for_champ($champion);
            }
        }
    }
 
    $best_winrate = 0;
    foreach ($champions as $winrate) {
        if ($winrate > $best_winrate) {
            $best_winrate = $winrate;
        }
    }
    echo array_search($best_winrate, $champions);
 
    /*$data = [];
    $data['Assassin'] = [];
    $data['Fighter'] = [];
    $data['Mage'] = [];
    $data['Support'] = [];
    $data['Tank'] = [];
    $data['Marksman'] = [];
 
    foreach($arr["data"] as $champion){
        //if champion has only one role, assign it to first tier of array
        if ($champion["tags"][1] == null) {
            array_push($data[$champion["tags"][0]], $champion["name"]);
        }
        //if champion has two roles, assign it to a second tier based off of secondary role
        else{
            //if haven't already created the secondary role array, create it
            if($data[$champion["tags"][0]][$champion["tags"][1]] == null){
                $data[$champion["tags"][0]][$champion["tags"][1]] = [];
            }
            array_push($data[$champion["tags"][0]][$champion["tags"][1]], $champion["name"]);
        }
    }*/
 
    /*foreach($data as $primaryRole => $role){
        foreach($role as $secondaryRole => $champion){
            if (is_array($champion)){
                foreach($champion as $c){
                    echo $c . ' has role of ' . $primaryRole . ' and ' . $secondaryRole . '<br>';
                }
            }
            else{
                echo $champion . ' has role of ' . $primaryRole . '<br>';
            }
        }
    }*/
}
 
//TODO: throw in a parameter to pull userName from current account logged in
/**
 * Crawls information from na.op.gg and returns an associative array to use to find most successful champion
 */
function crawl_info($summoner_name)
{
    $page = file_get_contents('http://na.op.gg/summoner/userName=' . $summoner_name);
    $doc = new DOMDocument();
    $doc->loadHTML($page);
    $spans = $doc->getElementsByTagName('span');
    $info = [];
    $info['kills'] = [];
    $info['deaths'] = [];
    $info['assists'] = [];
    $info['championName'] = [];
    $info['killParticipation'] = [];
    $info['gameResult'] = [];
    $info['cs'] = [];
    $info['csPerMin'] = [];
    $info['score'] = [];
    //we use these switches to ignore the first kda span, which is the 'average' span
    $killSwitch = False;
    $deathSwitch = False;
    $assistSwitch = False;
    foreach ($spans as $span) {
        //grab kda of past games
        if ($span->getAttribute('class') === 'Kill') {
            if (!preg_match('/[^\x20-\x7f]/', $span->nodeValue) && $killSwitch) {
                array_push($info['kills'], $span->nodeValue);
            }
            $killSwitch = True;
        }
        if ($span->getAttribute('class') === 'Death') {
            if (!preg_match('/[^\x20-\x7f]/', $span->nodeValue) && $deathSwitch) {
                array_push($info['deaths'], $span->nodeValue);
            }
            $deathSwitch = True;
        }
        if ($span->getAttribute('class') === 'Assist') {
            if (!preg_match('/[^\x20-\x7f]/', $span->nodeValue) && $assistSwitch) {
                array_push($info['assists'], $span->nodeValue);
            }
            $assistSwitch = True;
        }
        if ($span->getAttribute('class') === 'GameResult') {
            $gameResults = $span->getElementsbyTagName('span');
            foreach ($gameResults as $gameResult) {
                if ($gameResult->getAttribute('class') === 'Losses') {
                    array_push($info['gameResult'], 'loss');
                } else {
                    array_push($info['gameResult'], 'win');
                }
            }
        }
    }
 
    $divs = $doc->getElementsByTagName('div');
    foreach ($divs as $div) {
        if ($div->getAttribute('class') === 'ChampionImage') {
            $imgs = $div->getElementsbyTagName('img');
            //grab champion name by game
            foreach ($imgs as $img) {
                $championName = $img->getAttribute('src');
                $first_pos = strpos($championName, 'champions/');
                $first_pos += 10;
                $championName = str_replace('_square_0.png', '', $championName);
                array_push($info['championName'], substr($championName, $first_pos));
            }
        }
        if ($div->getAttribute('class') === 'Stats') {
            $stats_spans = $div->getElementsbyTagName('div');
            //grab kill participation by game
            foreach ($stats_spans as $stats_span) {
                if (strpos($stats_span->nodeValue, '%') !== False) {
                    array_push($info['killParticipation'], substr($stats_span->nodeValue, strpos($stats_span->nodeValue, '%') - 2, 2));
                }
                if (strpos($stats_span->nodeValue, 'CS') !== False) {
                    $cs = trim($stats_span->nodeValue);
                    $first_paren = strpos($cs, '(');
                    $second_paren = strpos($cs, ')');
                    $len = $second_paren - $first_paren - 1;
                    $cs_per_min = substr($cs, $first_paren + 1, $len);
                    array_push($info['cs'], substr($cs, 0, strpos($cs, '(') - 1));
                    array_push($info['csPerMin'], $cs_per_min);
                }
            }
        }
    }
    return $info;
}
 
 
/**
 * Returns the name of the most successful champion based on the information crawled from op.gg
 * @param $info array that should be created by crawling op.gg
 * @returns string -- most successful champion
 */
function most_successful_champ($info)
{
 
    $supports = ['Alistar', 'Bard', 'Blitzcrank', 'Braum', 'Janna', 'Karma', 'Leona', 'Morgana', 'Nami',
        'Nautilus', 'Nunu', 'Rammus', 'Sejuani', 'Shen', 'Sion', 'Sona', 'Soraka', 'TahmKench',
        'Taric', 'Thresh', 'Zilean', 'Zyra'];
    for ($i = 0; $i < sizeof($info['gameResult']); $i++) {
        $is_support = False;
        //check if our champion is a support
        foreach ($supports as $support) {
            if (strtolower($info['championName'][$i]) == strtolower($support)) {
                $is_support = True;
                break;
            }
        }
        //add kda to score
        //if our champ is a support, rank assists as worth more than kills, and rank deaths less important
        if ($is_support) {
            $revised_kda_score = round((.5 * $info['kills'][$i]) + $info['assists'][$i] - (.75 * $info['deaths'][$i]), 2);
        } else {
            $revised_kda_score = round($info['kills'][$i] + (.5 * $info['assists'][$i]) - $info['deaths'][$i], 2);
        }
        $info['score'][$i] += $revised_kda_score;
        //alter our score based on whether or not we won the game
        if ($info['gameResult'][$i] == 'win') {
            $info['score'][$i] += 7.5;
        } else {
            $info['score'][$i] -= 7.5;
        }
        //if our champ is a support, our cs per minute is less important, so boost it
        if ($is_support) {
            $info['score'][$i] += $info['csPerMin'][$i] + 5;
        } else {
            $info['score'][$i] += $info['csPerMin'][$i];
        }
        //kill participation is considered good if > 50%, else it reduces our score
        if ($info['killParticipation'][$i] >= 50) {
            $info['score'][$i] += ($info['killParticipation'][$i] / 10) * .5;
        } else {
            $info['score'][$i] -= ((50 - $info['killParticipation'][$i]) / 10);
        }
    }
 
    //compile our scores by champion
    $success_scores = [];
    for ($i = 0; $i < sizeof($info['score']); $i++) {
        $success_scores[$info['championName'][$i]] += $info['score'][$i];
    }
 
    $most_successful_score = 0;
    $most_successful_champ = 'NOPE';
    foreach ($success_scores as $success_score) {
        if ($success_score > $most_successful_score) {
            //the most successful champ is this success_score's key
            $most_successful_champ = array_search($success_score, $success_scores);
            $most_successful_score = $success_score;
        }
    }
    return $most_successful_champ;
}
 
function get_similar_champions($champName)
{
	$servername = "pickachamp.web.engr.illinois.edu";
    $serverUser = "pickacha_admin";
    $serverPassword = "admin";
    $dbname = "pickacha_db";
 
    //Create connection
    $conn = new mysqli($servername, $serverUser, $serverPassword, $dbname);
 
    //Check connection
    if (mysqli_connect_error()){
        die("Database failed: " . mysqli_connect_error());
    }
 
    $sql = "SELECT name, rating, similar1, similar2, similar3 FROM championList";
    $result = $conn->query($sql);
 
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            if(strtolower($row["name"]) == strtolower($champName)){
                $arr = [];
                array_push($arr, $row["similar1"]);
                array_push($arr, $row["similar2"]);
                array_push($arr, $row["similar3"]);
                return $arr;
            }
        }
    } else {
        //echo "0 results";
    }
    $conn->close();
    return null;
}
 
 
/**
 * Find the current winrate of a champion, based on leagueofgraphs.com
 *
 * @param $champName string the name of the champ we are trying to find the winrate for
 * @return string the winrate of the champ
 */
function get_winrate_for_champ($champName)
{
    error_reporting(0);
    $page = file_get_contents('http://www.leagueofgraphs.com/champions/stats/' . strtolower($champName));
    $doc = new DOMDocument();
    $doc->loadHTML($page);
    $divs = $doc->getElementsByTagName('div');
    foreach ($divs as $div) {
        if ($div->getAttribute('id') === 'graph2') {
            //chop off the % in the string
            return substr($div->nodeValue, 0, strlen($div->nodeValue) - 2);
        }
    }
    return null;
}

/**
 * Recommends by both match history and user ratings
 * @param $summoner_name
 */
function recommend_by_rating($summoner_name)
{
    $info = crawl_info($summoner_name);
    $successful_champ = most_successful_champ($info);
    $similar_champs = get_similar_champions($successful_champ);
    $ratings = [];
    foreach ($similar_champs as $similar_champ) {
        $ratings[$similar_champ] = get_rating_for_champ($similar_champ);
    }
    $best_rating = 0;
    $best_champ = '';
    foreach ($ratings as $rating) {
        if ($rating > $best_rating) {
            $best_rating = $rating;
            $best_champ = array_search($rating, $ratings);
        }
    }
    echo $best_champ;
}

/**
 * Find the current rating of a champion, based on our db
 */
function get_rating_for_champ($champName)
{
    $servername = "pickachamp.web.engr.illinois.edu";
    $serverUser = "pickacha_admin";
    $serverPassword = "admin";
    $dbname = "pickacha_db";
 
    //Create connection
    $conn = new mysqli($servername, $serverUser, $serverPassword, $dbname);
 
    //Check connection
    if (mysqli_connect_error()){
        die("Database failed: " . mysqli_connect_error());
    }
 
    $sql = "SELECT name, rating, similar1, similar2, similar3 FROM championList";
    $result = $conn->query($sql);
 
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            if(strtolower($row["name"]) == strtolower($champName)){
                return $row["rating"];
            }
        }
    } else {
        //echo "0 results";
    }
    $conn->close();
    return 0;
}
