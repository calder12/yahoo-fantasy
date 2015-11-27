<?php  
require("data.php"); 
?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title><?php echo $data['title'];?></title>
  <meta name="" content="">
  <link href='https://fonts.googleapis.com/css?family=Slabo+13px' rel='stylesheet' type='text/css'>
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="css/grid.css?v=1.0">
  <link rel="stylesheet" href="css/styles.css?v=1.0">
  <!--[if lt IE 9]>
  <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>

<body>
  <div class="content">
    <div class="row header">
      <div class="headaer span-12">
        <h1><?php echo $data['title'];?></h1>
      </div>
    </div>
    <div class="row">
      <div class="span12">
        <p>
          <span>Filters:</span> 
          <a href="#" class="switch" id="all">All</a>
          <?php
            foreach($data['leagues'] as $league){
              echo '<a href="#" class="switch" id="'.strtolower($league).'">'.$league.'</a> ';
            }
          ?>
        </p>
        <table>
          <tr>
            <th>User</th>
            <th>Record (w-l-t)</th>
            <th>Points</th>
            <th>Points Back</th>
            <th>League</th>
          </tr>
          <?php
            foreach($data['teams'] as $team){
              $pb         = $team['points'] - $data['leader']['points'];
              $leader     = $team['league_rank'] == 1 ? ' class="league-leader"' : '';
              $leader2    = $team['league_rank'] == 1 ? ' leader' : '';
              $league     = '<a target="_blank" href="http://hockey.fantasysports.yahoo.com/hockey/'.$team['league_id'].'">'.$team['league'].'</a>';
              $user       = '<a target="_blank" href="http://hockey.fantasysports.yahoo.com/hockey/'.$team['league_id'].'/'.$team['id'].'">'.$team['name'].'</a>';
              $table_row  = '<tr style="background-color:'.strtolower($team['bkcolour']).'" class="'.strtolower($team['league']).$leader2.' team-row">';
              $table_row .= '<td'.$leader.'>'.$user.'</td>';
              $table_row .= '<td class="text-center">'.$team['wins']. ' - '.$team['losses']. ' - '.$team['ties'].'</td>';
              $table_row .= '<td class="text-right">'.$team['points'].'</td>';
              $table_row .= '<td class="text-right">'.$pb.'</td>';
              $table_row .= '<td class="text-center">'.$league.'</td>';
              $table_row .= '</tr>';
              echo $table_row;
            }
          ?>
        </table>
      </div>
    </div>
  </div>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script>
  $('.switch').on('click', function(e){
    e.preventDefault();
    var trigger = $(this).attr('id');
    console.log(trigger)
    if(trigger != 'all'){
      $('.team-row').hide();
      $('.team-row').each(function(){
        if($(this).hasClass(trigger)){
          $(this).show();
        }
      });
    } else {
      $('.team-row').show();
    }
  });
  </script>
</body>
</html>