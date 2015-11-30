<?php

if($_POST['action'] == 'match_history') {
	$summoner_name = $_POST["summoner_name"];
	recommend_by_match_history($summoner_name);
} else if ($_POST['action'] == 'role'){
	$primary_role = $_POST["primary_role"];
	$secondary_role = $_POST["secondary_role"];
	recommend_by_role($primary_role, $secondary_role);
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
    $similar_champs = [];
    $similar_champs['Aatrox'] = ['Tryndamere', 'Jax', 'JarvanIV'];
    $similar_champs['Ahri'] = ['Akali', 'Leblanc', 'Kassadin'];
    $similar_champs['Akali'] = ['Ahri', 'Diana', 'Katarina'];
    $similar_champs['Alistar'] = ['Tahmkench', 'Leona', 'Braum'];
    $similar_champs['Amumu'] = ['Sejuani', 'Nautilus', 'Malphite'];
    $similar_champs['Anivia'] = ['Brand', 'Karthus', 'Cassiopeia'];
    $similar_champs['Ashe'] = ['Varus', 'Jinx', 'Caitlyn'];
    $similar_champs['Azir'] = ['Heimerdinger', 'Zyra', 'Xerath'];
    $similar_champs['Bard'] = ['Lulu', 'Karma', 'Thresh'];
    $similar_champs['Blitzcrank'] = ['Thresh', 'Leona', 'Nautilus'];
    $similar_champs['Brand'] = ['Xerath', 'Anivia', 'Annie'];
    $similar_champs['Braum'] = ['Alistar', 'Leona', 'Tahmkench'];
    $similar_champs['Caitlyn'] = ['Varus', 'Ashe', 'Jinx'];
    $similar_champs['Cassiopeia'] = ['Syndra', 'Ryze', 'Karthus'];
    $similar_champs['Chogath'] = ['Drmundo', 'Singed', 'Malphite'];
    $similar_champs['Corki'] = ['Ezreal', 'Kogmaw', 'Lucian'];
    $similar_champs['Darius'] = ['Garen', 'Riven', 'Renekton'];
    $similar_champs['Diana'] = ['Akali', 'Ekko', 'Fizz'];
    $similar_champs['Drmundo'] = ['Chogath', 'Singed', 'Zac'];
    $similar_champs['Draven'] = ['Graves', 'Caitlyn', 'Lucian'];
    $similar_champs['Ekko'] = ['Diana', 'Fizz', 'Zilean'];
    $similar_champs['Elise'] = ['Evelynn', 'Nidalee', 'Jayce'];
    $similar_champs['Evelynn'] = ['Diana', 'Elise', 'Shaco'];
    $similar_champs['Ezreal'] = ['Corki', 'Kogmaw', 'Lucian'];
    $similar_champs['Fiddlesticks'] = ['Kennen', 'Morgana', 'Zyra'];
    $similar_champs['Fiora'] = ['Riven', 'Irelia', 'Jax'];
    $similar_champs['Fizz'] = ['Diana', 'Akali', 'Kassadin'];
    $similar_champs['Galio'] = ['Swain', 'Gragas', 'Chogath'];
    $similar_champs['Gangplank'] = ['Pantheon', 'Yorick', 'Tryndamere'];
    $similar_champs['Garen'] = ['Darius', 'Renekton', 'Shyvana'];
    $similar_champs['Gnar'] = ['Olaf', 'Shyvana', 'Jayce'];
    $similar_champs['Gragas'] = ['Nautilus', 'Galio', 'Maokai'];
    $similar_champs['Graves'] = ['Lucian', 'Draven', 'Urgot'];
    $similar_champs['Hecarim'] = ['Shyvana', 'Skarner', 'Renekton'];
    $similar_champs['Heimerdinger'] = ['Azir', 'Zyra', 'Orianna'];
    $similar_champs['Illaoi'] = ['Darius', 'Garen', 'Renekton'];
    $similar_champs['Irelia'] = ['Jax', 'Xinzhao', 'Fiora'];
    $similar_champs['Janna'] = ['Nami', 'Karma', 'Soraka'];
    $similar_champs['JarvanIV'] = ['Wukong', 'Nautilus', 'Xinzhao'];
    $similar_champs['Jax'] = ['Fiora', 'Irelia', 'Poppy'];
    $similar_champs['Jayce'] = ['Nidalee', 'Leesin', 'Elise'];
    $similar_champs['Jinx'] = ['Tristana', 'Caitlyn', 'Kogmaw'];
    $similar_champs['Kalista'] = ['Kindred', 'Tristana', 'Lucian'];
    $similar_champs['Karma'] = ['Orianna', 'Lulu', 'Zilean'];
    $similar_champs['Karthus'] = ['Cassiopeia', 'Anivia', 'Syndra'];
    $similar_champs['Kassadin'] = ['Leblanc', 'Fizz', 'Akali'];
    $similar_champs['Katarina'] = ['Akali', 'Ahri', 'Diana'];
    $similar_champs['Kayle'] = ['Gnar', 'Lulu', 'Jayce'];
    $similar_champs['Kennen'] = ['Fiddlesticks', 'Veigar', 'Amumu'];
    $similar_champs['Khazix'] = ['Zed', 'Rengar', 'Talon'];
    $similar_champs['Kindred'] = ['Kalista', 'Lucian', 'Vayne'];
    $similar_champs['KogMaw'] = ['Tristana', 'Twitch', 'Corki'];
    $similar_champs['Leblanc'] = ['Kassadin', 'Ahri', 'Zed'];
    $similar_champs['LeeSin'] = ['Riven', 'Zed', 'JarvanIV'];
    $similar_champs['Leona'] = ['Thresh', 'Braum', 'Alistar'];
    $similar_champs['Lissandra'] = ['Morgana', 'Orianna', 'Leblanc'];
    $similar_champs['Lucian'] = ['Graves', 'Ezreal', 'Draven'];
    $similar_champs['Lulu'] = ['Karma', 'Janna', 'Sona'];
    $similar_champs['Lux'] = ['Morgana', 'Zyra', 'Xerath'];
    $similar_champs['Malphite'] = ['Maokai', 'Chogath', 'Amumu'];
    $similar_champs['Malzahar'] = ['Brand', 'Cassiopeia', 'Swain'];
    $similar_champs['Maokai'] = ['Malphite', 'Chogath', 'Amumu'];
    $similar_champs['MasterYi'] = ['Fiora', 'Tryndamere', 'Yasuo'];
    $similar_champs['MissFortune'] = ['Graves', 'Lucian', 'Varus'];
    $similar_champs['Mordekaiser'] = ['Vladimir', 'Rumble', 'Yorick'];
    $similar_champs['Morgana'] = ['Lux', 'Zyra', 'Lissandra'];
    $similar_champs['Nami'] = ['Sona', 'Janna', 'Zyra'];
    $similar_champs['Nasus'] = ['Renekton', 'Sion', 'Singed'];
    $similar_champs['Nautilus'] = ['Blitzcrank', 'Thresh', 'Amumu'];
    $similar_champs['Nidalee'] = ['Jayce', 'Elise', 'Ekko'];
    $similar_champs['Nocturne'] = ['Vi', 'Hecarim', 'RekSai'];
    $similar_champs['Nunu'] = ['Galio', 'Blitzcrank', 'Fiddlesticks'];
    $similar_champs['Olaf'] = ['DrMundo', 'Darius', 'Trundle'];
    $similar_champs['Orianna'] = ['Syndra', 'Lux', 'Zyra'];
    $similar_champs['Pantheon'] = ['Talon', 'XinZhao', 'Nocturne'];
    $similar_champs['Poppy'] = ['Jax', 'Olaf', 'Irelia'];
    $similar_champs['Quinn'] = ['Vayne', 'Twitch', 'Nidalee'];
    $similar_champs['Rammus'] = ['Hecarim', 'Skarner', 'Olaf'];
    $similar_champs['Reksai'] = ['Nocturne', 'Vi', 'Khazix'];
    $similar_champs['Renekton'] = ['Nasus', 'Riven', 'Shyvana'];
    $similar_champs['Rengar'] = ['Khazix', 'Shaco', 'LeeSin'];
    $similar_champs['Riven'] = ['Renekton', 'LeeSin', 'Rengar'];
    $similar_champs['Rumble'] = ['Elise', 'Mordekaiser', 'Zac'];
    $similar_champs['Ryze'] = ['Annie', 'Swain', 'Vladimir'];
    $similar_champs['Sejuani'] = ['Amumu', 'Maokai', 'Hecarim'];
    $similar_champs['Shaco'] = ['Evelynn', 'Rengar', 'Talon'];
    $similar_champs['Shen'] = ['Malphite', 'Chogath', 'Zac'];
    $similar_champs['Shyvana'] = ['Renekton', 'Nasus', 'Udyr'];
    $similar_champs['Singed'] = ['Volibear', 'Udyr', 'Shyvana'];
    $similar_champs['Sion'] = ['Maokai', 'Nasus', 'Darius'];
    $similar_champs['Sivir'] = ['Lucian', 'KogMaw', 'Vayne'];
    $similar_champs['Skarner'] = ['Hecarim', 'Volibear', 'Olaf'];
    $similar_champs['Sona'] = ['Nami', 'Janna', 'Soraka'];
    $similar_champs['Soraka'] = ['Nami', 'Sona', 'Janna'];
    $similar_champs['Swain'] = ['Fiddlesticks', 'Vladimir', 'Galio'];
    $similar_champs['Syndra'] = ['Orianna', 'Cassiopeia', 'Brand'];
    $similar_champs['TahmKench'] = ['Bard', 'Morgana', 'Braum'];
    $similar_champs['Talon'] = ['Zed' . 'Rengar', 'Khazix'];
    $similar_champs['Taric'] = ['Leona', 'Alistar', 'Morgana'];
    $similar_champs['Teemo'] = ['Kennen', 'Twitch', 'Ziggs'];
    $similar_champs['Thresh'] = ['Blitzcrank', 'Nautilus', 'Leona'];
    $similar_champs['Tristana'] = ['Vayne', 'KogMaw', 'Corki'];
    $similar_champs['Trundle'] = ['Udyr', 'Warwick', 'Anivia'];
    $similar_champs['Tryndamere'] = ['Aatrox', 'MasterYi', 'Fiora'];
    $similar_champs['TwistedFate'] = ['Veigar', 'Orianna', 'Fizz'];
    $similar_champs['Twitch'] = ['KogMaw', 'Teemo', 'Vayne'];
    $similar_champs['Udyr'] = ['Volibear', 'Trundle', 'Shyvana'];
    $similar_champs['Urgot'] = ['Graves', 'Twitch', 'Cassiopeia'];
    $similar_champs['Varus'] = ['MissFortune', 'Ashe', 'Jinx'];
    $similar_champs['Vayne'] = ['Quinn', 'Kalista', 'Jinx'];
    $similar_champs['Veigar'] = ['TwistedFate', 'Orianna', 'Fizz'];
    $similar_champs['Velkoz'] = ['Xerath', 'Brand', 'Lux'];
    $similar_champs['Vi'] = ['JarvanIV', 'Nocturne', 'Aatrox'];
    $similar_champs['Viktor'] = ['Xerath', 'Syndra', 'Lux'];
    $similar_champs['Vladimir'] = ['Mordekaiser', 'Swain', 'Ryze'];
    $similar_champs['Volibear'] = ['Singed', 'Udyr', 'DrMundo'];
    $similar_champs['Warwick'] = ['Trundle', 'Udyr', 'Skarner'];
    $similar_champs['Wukong'] = ['Vi', 'Riven', 'JarvanIV'];
    $similar_champs['Xerath'] = ['Lux', 'Viktor', 'Brand'];
    $similar_champs['XinZhao'] = ['JarvanIV', 'Irelia', 'Aatrox'];
    $similar_champs['Yasuo'] = ['MasterYi', 'Riven', 'Zed'];
    $similar_champs['Yorick'] = ['Vladimir', 'Malphite', 'Mordekaiser'];
    $similar_champs['Zac'] = ['JarvanIV', 'Chogath', 'DrMundo'];
    $similar_champs['Zed'] = ['Leblanc', 'Khazix', 'Talon'];
    $similar_champs['Ziggs'] = ['Gragas', 'Lux', 'Syndra'];
    $similar_champs['Zilean'] = ['Ekko', 'Ziggs', 'Kayle'];
    $similar_champs['Zyra'] = ['Lux', 'Orianna', 'Morgana'];
 
    $idx = rand(1, 3);
    foreach ($similar_champs as $key => $value) {
        if (strtolower($champName) == strtolower($key)) {
            return $value;
        }
    }
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
