<?php
require_once("db_connect.php");
setlocale(LC_ALL, 'fr_FR');
session_start();

$PHASE_PRONO = 0;
$PHASE_GROUP = 1;
$PHASE_FINAL = 2;

function kick_out_intruders($admin){
 if ($_SESSION['login']){
   if($admin AND $_SESSION['privilege']!='admin')
	  header("Location:index.php?out=rights");
 } else {
   header("Location:index.php?out=intru");
 }
}

function log_as($p){
	if (isset($_GET['log_as']) && get_phase()==$p ){
		$_SESSION['real_ID']=$_SESSION['current_ID'];
		$_SESSION['current_ID']=$_GET['log_as'];
	}
}

function un_log_as($p){
	if(isset($_GET['log_as']) && get_phase()==$p) 
		$_SESSION['current_ID']=$_SESSION['real_ID'];
}

function print_html_header($title, $with_menus){
  echo "<!doctype html>";
  echo "<html>";
  echo "<head>";
  echo "<link href='https://fonts.googleapis.com/css?family=Passion+One:700' rel='stylesheet' type='text/css'>";
  echo "<title>".$title."</title>";
  echo "<meta httpequiv=\"ContentType\" content=\"text/html; charset=windows-1252\" />";
  echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1\">";
  echo "<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css\" integrity=\"sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7\" crossorigin=\"anonymous\">";
  echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"style_div.css\">";
  echo "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js\"></script>";
  echo "<script src=\"http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js\"></script>";
  echo "<script type=\"text/javascript\" src=\"//cdn.ckeditor.com/4.5.9/basic/ckeditor.js\"></script>";
  echo "</head>";
  echo "<body>";
  echo "<div class=\"container top-container\">";
  echo "<div class=\"row\"><div class=\"header\"> <img src=\"https://cdn.rawgit.com/ericleib/pronocave/master/banniere.jpg\"></img><h1><span class='wrapper'><span style=\"color:blue;\">PRONO</span>CAVE <span style=\"color:red;\">2016</span></span></h1></div></div>";
  if($with_menus){
	print_menus();
  }
  echo "<div class=\"container\">";
  echo "<div class=\"row\">";
}

function print_html_footer($with_menus){
  echo "</div>";
  echo "</div>";
  echo "</div>";
  echo "</body>";
  echo "</html>";
}


function print_menus(){
  echo "<nav class=\"navbar navbar-default\">";
  echo "<ul class=\"nav navbar-nav\">";
  echo "<li><a href=\"main_page.php\">Accueil</a></li>";
  echo "<li><a href=\"regles.php\">Règles</a></li>";
  echo "<li class=\"dropdown\"><a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\">Pronostics <span class=\"caret\"></span></a>";
  echo "<ul class=\"dropdown-menu\">";
  echo "<li><a href=\"paris.php\">Phase de Groupes</a></li>";
  echo "<li><a href=\"paris_finales.php\">Phase Finale</a></li>";        
  echo "</ul></li>";
  echo "<li><a href=\"matchs.php\">Matchs</a></li>";
  echo "<li><a href=\"index.php?out=deco\">Déconnexion</a></li>";
  echo "</ul>";
  echo "</nav>";
}

// Fonction pour récupérer le nom d'une équipe
function get_name($id_team){
  $r = mysql_query("SELECT name FROM prono_teams WHERE id_team=$id_team")
                or die(mysql_error());
  $name = mysql_fetch_array($r);
  return $name['name'];
}

function get_user_name(){
  $id_user = $_SESSION['current_ID'];
  $q = mysql_fetch_array(mysql_query("SELECT login FROM prono_users WHERE id_user=$id_user"));
  return $q['login'];
}

// Fonction pour récupérer la phase
function get_phase(){
	$q = "SELECT value_int FROM prono_vars WHERE name='phase'";
	$r = mysql_query($q) or die(mysql_error());
	$val = mysql_fetch_array($r);
	return $val['value_int'];
}

function set_phase($phase){
	$q = "UPDATE prono_vars SET value_int=$phase WHERE name='phase'";
	return mysql_query($q) or die(mysql_error());
}

function get_playing_teams($poule){
  $teams = array();
  $q = "SELECT id_team,name FROM prono_teams WHERE poule='$poule' AND playing=1";
  $r = mysql_query($q) or die(mysql_error());
  return mysql_to_array($r);
}

function mysql_to_array($res){
  $results = array();
  while($r = mysql_fetch_array($res)){
	$results[] = $r;
  }
  return $results;
}

function get_matchs_finals($phase){
  $q = "SELECT * FROM prono_matchs WHERE phase='$phase' AND done>2 AND done<=5  ORDER BY poule ASC";
  $r_matchs = mysql_query($q) or die(mysql_error());
  return mysql_to_array($r_matchs);
}

function print_select_teams($teams, $A_or_B){
	echo "<select name='id_team_".$A_or_B."'>";
	foreach($teams as $team){
	  $id_team = $team['id_team'];
	  $name = $team['name'];
	  echo "<option value='$id_team'>".$name;
	}
	echo "</select>";
}

function append_match_data($match, $team_A, $team_B, $poules_A, $poules_B){
	$match['team_A'] = $team_A;
    $match['team_B'] = $team_B;
	$match['poule_A'] = $poules_A;
    $match['poule_B'] = $poules_B;
	if(is_array($poules_A)){
		$match['teams_A'] = array();
		$match['teams_B'] = array();
		foreach($poules_A as $poule){
		  $match['teams_A'] = array_merge($match['teams_A'], get_playing_teams($poule));
		}
		foreach($poules_B as $poule){
		  $match['teams_B'] = array_merge($match['teams_B'], get_playing_teams($poule));
		}
	}
	return $match;
}

function get_matchs_8emes(){
  $results = get_matchs_finals('8emes');
  $results[0] = append_match_data($results[0], "2-A", "2-C", 	 array("A"), array("C"));
  $results[1] = append_match_data($results[1], "1-D", "3-B/E/F", array("D"), array("B","E","F"));
  $results[2] = append_match_data($results[2], "1-B", "3-A/C/D", array("B"), array("A","C","D"));
  $results[3] = append_match_data($results[3], "1-F", "2-E",	 array("F"), array("E"));
  $results[4] = append_match_data($results[4], "1-C", "3-A/B/F", array("C"), array("A","B","F"));
  $results[5] = append_match_data($results[5], "1-E", "2-D",	 array("E"), array("D"));
  $results[6] = append_match_data($results[6], "1-A", "3-C/D/E", array("A"), array("C","D","E"));
  $results[7] = append_match_data($results[7], "2-B", "2-F",	 array("B"), array("F"));
  return $results;
}

function get_matchs_4rts(){
  $results = get_matchs_finals('4rts');
  $results[0] = append_match_data($results[0], "HF1", "HF2", "1", "2");
  $results[1] = append_match_data($results[1], "HF3", "HF4", "3", "4");
  $results[2] = append_match_data($results[2], "HF5", "HF6", "5", "6");
  $results[3] = append_match_data($results[3], "HF7", "HF8", "7", "8");
  return $results;
}

function get_matchs_demis(){
  $results = get_matchs_finals('demis');
  $results[0] = append_match_data($results[0], "QF1", "QF2", "1", "2");
  $results[1] = append_match_data($results[1], "QF3", "QF4", "3", "4");
  return $results;
}

function get_match_finale(){
  $results = get_matchs_finals('finale');
  $results[0] = append_match_data($results[0], "DF1", "DF2", "1", "2");
  return $results[0];
}

function get_paris($id_match, $id_user){
    $q_paris = "SELECT * FROM prono_paris WHERE id_user=$id_user AND id_match=$id_match";
    $r_paris = mysql_query($q_paris) or die(mysql_error());
	return mysql_to_array($r_paris);
}

function get_paris_for_final_match($id_user, $final_phase, $poule){
	$q = "SELECT prono_matchs.id_match, win, prono_paris.id_team_A, prono_paris.id_team_B FROM prono_matchs,prono_paris
		   WHERE phase='$final_phase' AND poule='$poule' AND prono_matchs.id_match=prono_paris.id_match AND id_user=$id_user";
	$r = mysql_query($q) or die(mysql_error());
	return mysql_to_array($r);
}

function create_finals(){
  $q = "INSERT INTO prono_matchs(phase,poule,done,date)
  		VALUES ('8emes','1',3,'2016-06-25 15:00:00'),
  			   ('8emes','2',3,'2016-06-25 21:00:00'),
  		       ('8emes','3',3,'2016-06-25 18:00:00'),
  		       ('8emes','4',3,'2016-06-26 21:00:00'),
  		       ('8emes','5',3,'2016-06-26 18:00:00'),
  		       ('8emes','6',3,'2016-06-27 18:00:00'),
  		       ('8emes','7',3,'2016-06-26 15:00:00'),
  		       ('8emes','8',3,'2016-06-27 21:00:00'),
  		       ('4rts','1',3,'2016-06-30 21:00:00'),
  		       ('4rts','2',3,'2016-07-01 21:00:00'),
  		       ('4rts','3',3,'2016-07-02 21:00:00'),
  		       ('4rts','4',3,'2016-07-03 21:00:00'),
  		       ('demis','1',3,'2016-07-06 21:00:00'),
  		       ('demis','2',3,'2016-07-07 21:00:00'),
  		       ('finale','1',3,'2016-07-10 21:00:00')";
  return mysql_query($q) or die(mysql_error());
}

function create_table_users(){
  $q = "CREATE TABLE `prono_users` (
  `id_user` int(10) unsigned NOT NULL auto_increment,
  `login` varchar(50) NOT NULL default '',
  `pass` varchar(50) NOT NULL default '',
  `score` int(10) NOT NULL default 0,
  `bonus` int(10) NOT NULL default 0,
  `privilege` varchar(50) NOT NULL default 'none',
  `mail` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id_user`)
  ) TYPE=MyISAM ;";
  return mysql_query($q) or die(mysql_error());
}

function create_table_teams(){
  $q = "CREATE TABLE `prono_teams` (
  `id_team` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `poule` char NOT NULL default '',
  `playing` int(1) NOT NULL default 1,
  PRIMARY KEY  (`id_team`)
  ) TYPE=MyISAM ;";
  return mysql_query($q) or die(mysql_error());
}

function create_table_matchs(){
  $q = "CREATE TABLE `prono_matchs` (
  `id_match` int(10) unsigned NOT NULL auto_increment,
  `id_team_A` int(10) NOT NULL,
  `id_team_B` int(10) NOT NULL,
  `score_A` int(10) NULL,
  `score_B` int(10) NULL,
  `done` int(1) NOT NULL default 0,
  `phase` varchar(10) NOT NULL default 'poules',
  `poule` varchar(4) NOT NULL default '',
  `penalties` int(1) NOT NULL default 0,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id_match`)
  ) TYPE=MyISAM ;";
  return mysql_query($q) or die(mysql_error());
}

function create_table_paris(){
  $q = "CREATE TABLE = `prono_paris` (
  `id_pari` int(10) unsigned NOT NULL auto_increment,
  `id_match` int(10) NOT NULL,
  `id_user` int(10) NOT NULL,
  `pari_A` int(10) NOT NULL,
  `pari_B` int(10) NOT NULL,
  `id_team_A` int(10) NOT NULL,
  `id_team_B` int(10) NOT NULL,
  `win` varchar(4) collate latin1_general_ci NOT NULL,
  `penalties` int(1) NOT NULL default '0',
  `points` int(2) default NULL,
  PRIMARY KEY  (`id_pari`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=2461 ;";
  return mysql_query($q) or die(mysql_error());
}

function create_table_messages(){
  $q = "CREATE TABLE `prono_messages` (
  `id_message` int(10) unsigned NOT NULL auto_increment,
  `login` varchar(50) NOT NULL,
  `text` longtext NOT NULL default '',
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id_message`)
  ) TYPE=MyISAM ;";
  return mysql_query($q) or die(mysql_error());
}

function create_table_vars(){
  $q = "CREATE TABLE `prono_vars` (
  `id_var` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default 'default',
  `value_int` int(10),
  `value_char` varchar(50),
  PRIMARY KEY  (`id_var`)
  ) TYPE=MyISAM ;";
  return mysql_query($q) or die(mysql_error());
}

?>