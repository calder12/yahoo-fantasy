<?php 
require("stuff/OAuth.php");
$config = json_decode(file_get_contents('stuff/creds.json'));

$leagues = $config[1]->leagues;

$newTeam = array();
$i = 0;
$j = 0;
foreach($leagues as $league){
  $url = 'http://fantasysports.yahooapis.com/fantasy/v2/league/'.$config[0]->game_key.'.l.'.$league.'/standings';  
  $args = array();  
  $args["q"] = "yahoo";  
  $args["format"] = "json";  

  $consumer = new OAuthConsumer($config[0]->consumer_key, $config[0]->consumer_secret);  
  $request = OAuthRequest::from_consumer_and_token($consumer, NULL,"GET", $url, $args);  
  $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL);  
  $url = sprintf("%s?%s", $url, OAuthUtil::build_http_query($args));  
  $ch = curl_init();  
  $headers = array($request->to_header());  
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  
  curl_setopt($ch, CURLOPT_URL, $url);  
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);  
  $rsp = curl_exec($ch);  
  $results = json_decode($rsp);  

  $teams = $results->fantasy_content->league[1]->standings[0]->teams;

  $league_names[$j] = str_replace('TwentyBucks-', '', $results->fantasy_content->league[0]->name);
  // echo '<xmp>';
  // print_r($teams);

  $division1 = array(1, 6, 3, 11, 13, 9, 10); // Marner/Will Nye
  $division2 = array(12, 14, 8, 7, 2, 4, 5); // Marner/ Matthews
  $division3 = array(9, 11, 12, 13, 2, 3, 1); // McDavid/Jack Eichel
  $division4 = array(10, 14, 8, 7, 4, 5, 6); //McDavid/Artemi Panarin
  $data['divisions'] = array(
    array('Marner - Will Nye', 'Nye'),
    array('Marner - Matthews', 'matthews'),
    array('McDavid - Eichel', 'eichel'),
    array('McDavid - Panarin', 'panarin'),
  );

  foreach($teams as $team){
    if(isset($team->team[0][2]->name)){
      $currentLeague = substr($team->team[0][0]->team_key, 6,5);
      $newTeam[$i]['id']          = $team->team[0][1]->team_id;
      $newTeam[$i]['name']        = $team->team[0][2]->name;
      $newTeam[$i]['league_rank'] = $team->team[2]->team_standings->rank;
      $newTeam[$i]['wins']        = $team->team[2]->team_standings->outcome_totals->wins;
      $newTeam[$i]['losses']      = $team->team[2]->team_standings->outcome_totals->losses;
      $newTeam[$i]['ties']        = $team->team[2]->team_standings->outcome_totals->ties;
      $newTeam[$i]['name']        = $team->team[0][2]->name;
      $newTeam[$i]['points']      = $team->team[1]->team_points->total;
      $newTeam[$i]['league']      = $league_names[$j];
      $newTeam[$i]['league_id']   = $league;
      if($currentLeague == 18156 && in_array($team->team[0][1]->team_id, $division1)) {
        $newTeam[$i]['bkcolour']    = '#57BCD9';
        $newTeam[$i]['division_name'] = $data['divisions'][0][0];
        $newTeam[$i]['division'] = $data['divisions'][0][1];
      }
      if($currentLeague == 18156 && in_array($team->team[0][1]->team_id, $division2)) {
        $newTeam[$i]['bkcolour']    = '#72FE95';
        $newTeam[$i]['division_name'] = $data['divisions'][1][0];
        $newTeam[$i]['division'] = $data['divisions'][1][1];
      }
      if($currentLeague == 30523 && in_array($team->team[0][1]->team_id, $division3)) {
        $newTeam[$i]['bkcolour']    = '#C4ABFE';
        $newTeam[$i]['division_name'] = $data['divisions'][2][0];
        $newTeam[$i]['division'] = $data['divisions'][2][1];
      }
      if($currentLeague == 30523 && in_array($team->team[0][1]->team_id, $division4)) {
        $newTeam[$i]['bkcolour']    = '#FFCECE';
        $newTeam[$i]['division_name'] = $data['divisions'][3][0];
        $newTeam[$i]['division'] = $data['divisions'][3][1];
      }
      
      $i++;
    }
  } 
  $j++;
}

usort($newTeam, function($a, $b) {
    if($a['points']==$b['points']) return 0;
    return $a['points'] < $b['points']?1:-1;
});

$data['title']    = $config[0]->site_title;
$data['teams']    = $newTeam;
$data['leader']   = $newTeam[0];
$data['leagues']  = $league_names;
$data['colours']  = $config[2]->colours;

return $data;