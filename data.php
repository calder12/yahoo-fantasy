<?php 
require("stuff/OAuth.php");  
$string = file_get_contents('stuff/creds.json');
$config = json_decode($string);

$leagues = $config[1]->leagues;

$newTeam = array();
$i = 0;
$j = 0;
foreach($leagues as $league){
  $url = 'http://fantasysports.yahooapis.com/fantasy/v2/league/352.l.'.$league.'/standings';  
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

  $league_names[$j] = str_replace('TenBucks-', '', $results->fantasy_content->league[0]->name);
  // echo '<xmp>';
  // print_r($results);

  foreach($teams as $team){
    if($team->team[0][2]->name){
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
      $newTeam[$i]['bkcolour']    = $config[2]->colours[$j];
      $i++;
    }
  } 
  $j++;
}
usort($newTeam, function($a, $b) {
    if($a['points']==$b['points']) return 0;
    return $a['points'] < $b['points']?1:-1;
});

$data['title']    = $config[0]->siteTitle;
$data['teams']    = $newTeam;
$data['leader']   = $newTeam[0];
$data['leagues']  = $league_names;
$data['colours']  = $config[2]->colours;

return $data;