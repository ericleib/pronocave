<?php require_once("db_connect.php"); ?>
<?php session_start();

if ($_SESSION['login']){
  if($_SESSION['privilege']!='admin')
	header("Location:index.php?out=rights");
}
else {
header("Location:index.php?out=intru");
}?>

<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
       <title>Administration</title>
<meta httpequiv="ContentType" content="text/html; charset=windows-1252" />
<link rel="shortcut icon" href="BallonFoot.gif" type="image/x-icon"/>
<link rel="icon" href="BallonFoot.gif" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" href="style_div.css">
</head>
<body>

	<div class="header"> <img src="banniere.jpg" width=410 height=120> </div>
<hr>
<?php
if(isset($_POST['create_table_users'])){
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
  $result=mysql_query($q) or die(mysql_error());
  echo "Table cr��e : ".$result;
}


if(isset($_POST['reset_scores_users'])){
  $q = "UPDATE prono_users SET score=0";
  $result=mysql_query($q) or die(mysql_error());
  echo "Scores mis � 0 : ".$result;
}

if(isset($_POST['del_user'])){
  $del_user = addslashes($_POST['del_user']);
  $q = "DELETE FROM prono_users WHERE login='$del_user'";
  $result=mysql_query($q) or die(mysql_error());
  echo "User supprim� : ".$result;
}

if(isset($_POST['adm_user'])){
  $adm_user = addslashes($_POST['adm_user']);
  $q = "UPDATE prono_users SET privilege='admin' WHERE login='$adm_user'";
  $result=mysql_query($q) or die(mysql_error());
  echo "User admin : ".$result;
}

if(isset($_POST['unadm_user'])){
  $unadm_user = addslashes($_POST['unadm_user']);
  $q = "UPDATE prono_users SET privilege='none' WHERE login='$unadm_user'";
  $result=mysql_query($q) or die(mysql_error());
  echo "User un-admin : ".$result;
}

if(isset($_POST['create_table_teams'])){
  $q = "CREATE TABLE `prono_teams` (
  `id_team` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `poule` char NOT NULL default '',
  `playing` int(1) NOT NULL default 1,
  PRIMARY KEY  (`id_team`)
  ) TYPE=MyISAM ;";
  $result=mysql_query($q) or die(mysql_error());
  echo "Table cr��e : ".$result;
}

if(isset($_POST['create_table_matchs'])){
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
  $result=mysql_query($q) or die(mysql_error());
  echo "Table cr��e : ".$result;
}

if(isset($_POST['create_table_paris'])){
  $q = "CREATE TABLE `prono_paris` (
  `id_pari` int(10) unsigned NOT NULL auto_increment,
  `id_match` int(10) NOT NULL,
  `id_user` int(10) NOT NULL,
  `pari_A` int(10) NOT NULL,
  `pari_B` int(10) NOT NULL,
  `id_team_A` int(10),
  `id_team_B` int(10),
  `win` varchar(4) NOT NULL,
  `points` int(2) NULL,
  PRIMARY KEY  (`id_pari`)
  ) TYPE=MyISAM ;";
  $result=mysql_query($q) or die(mysql_error());
  echo "Table cr��e : ".$result;
}


if(isset($_POST['create_table_messages'])){
  $q = "CREATE TABLE `prono_messages` (
  `id_message` int(10) unsigned NOT NULL auto_increment,
  `login` varchar(50) NOT NULL,
  `text` longtext NOT NULL default '',
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id_message`)
  ) TYPE=MyISAM ;";
  $result=mysql_query($q) or die(mysql_error());
  echo "Table cr��e : ".$result;
}


if(isset($_POST['create_table_vars'])){
  $q = "CREATE TABLE `prono_vars` (
  `id_var` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default 'default',
  `value_int` int(10),
  `value_char` varchar(50),
  PRIMARY KEY  (`id_var`)
  ) TYPE=MyISAM ;";
  $result=mysql_query($q) or die(mysql_error());
  echo "Table cr��e : ".$result;
}


if(isset($_POST['create_final_matchs'])){
  $q = "INSERT INTO prono_matchs(phase,poule,done,date)
  		VALUES ('8emes','1A',3,'2010-06-26 16:00:00'),
  			   ('8emes','1C',3,'2010-06-26 20:30:00'),
  		       ('8emes','2C',3,'2010-06-27 16:00:00'),
  		       ('8emes','2A',3,'2010-06-27 20:30:00'),
  		       ('8emes','1E',3,'2010-06-28 16:00:00'),
  		       ('8emes','1G',3,'2010-06-28 20:30:00'),
  		       ('8emes','2E',3,'2010-06-29 16:00:00'),
  		       ('8emes','2G',3,'2010-06-29 20:30:00'),
  		       ('4rts','1E',3,'2010-07-02 16:00:00'),
  		       ('4rts','1A',3,'2010-07-02 20:30:00'),
  		       ('4rts','2A',3,'2010-07-03 16:30:00'),
  		       ('4rts','2E',3,'2010-07-03 20:30:00'),
  		       ('demis','1',3,'2010-07-06 20:30:00'),
  		       ('demis','2',3,'2010-07-07 20:30:00'),
  		       ('finale','1',3,'2010-07-11 20:30:00')";
  $result=mysql_query($q) or die(mysql_error());
  echo "Matchs cr��s : ".$result;
}

if(isset($_POST['change_phase'])){
  $phase = $_POST['change_phase'];
  $q = "SELECT id_var FROM prono_vars WHERE name='phase'";
  $r =mysql_query($q) or die(mysql_error());
  if(mysql_num_rows($r)==0){
	$q = "INSERT INTO prono_vars(value_int,name) VALUES($phase,'phase')";
    $result=mysql_query($q) or die(mysql_error());
  }
  else{
    $q = "UPDATE prono_vars SET value_int=$phase WHERE name='phase'";
    $result=mysql_query($q) or die(mysql_error());
  }
  echo "Phase chang�e: ".$result;
}

if(isset($_POST['update_wins_poule'])){
  $poule = $_POST['update_wins_poule'];
  $id_team_A= $_POST['id_team_A'];
  $id_team_B= $_POST['id_team_B'];
  $q = "UPDATE prono_matchs SET id_team_A=$id_team_A,id_team_B=$id_team_B,done=4
        WHERE poule='$poule' AND done=3 AND phase='8emes'";
  $result=mysql_query($q) or die(mysql_error());
  echo "match de 8emes enregistr�: ".$result;
}

if(isset($_POST['update_wins_8emes'])){
  $poule = $_POST['update_wins_8emes'];
  $id_team_A= $_POST['id_team_A'];
  $id_team_B= $_POST['id_team_B'];
  $q = "UPDATE prono_matchs SET id_team_A=$id_team_A,id_team_B=$id_team_B,done=4
        WHERE poule='$poule' AND done=3 AND phase='4rts'";
  $result=mysql_query($q) or die(mysql_error());
  echo "match de quarts enregistr�: ".$result;
}

if(isset($_POST['update_wins_4rts'])){
  $poule = $_POST['update_wins_4rts'];
  $id_team_A= $_POST['id_team_A'];
  $id_team_B= $_POST['id_team_B'];
  $q = "UPDATE prono_matchs SET id_team_A=$id_team_A,id_team_B=$id_team_B,done=4
        WHERE poule='$poule' AND done=3 AND phase='demis'";
  $result=mysql_query($q) or die(mysql_error());
  echo "match de demi-finale enregistr�: ".$result;
}

if(isset($_POST['update_wins_demis'])){
  $poule = $_POST['update_wins_demis'];
  $id_team_A= $_POST['id_team_A'];
  $id_team_B= $_POST['id_team_B'];
  $q = "UPDATE prono_matchs SET id_team_A=$id_team_A,id_team_B=$id_team_B,done=4
        WHERE poule='$poule' AND done=3 AND phase='finale'";
  $result=mysql_query($q) or die(mysql_error());
  echo "match de finale enregistr�: ".$result;
}

if(isset($_POST['eliminate_teams'])){
  $q = "UPDATE prono_teams SET playing=0";
  mysql_query($q) or die(mysql_error());
  $q = "SELECT * FROM prono_matchs
        WHERE (phase='8emes'OR phase='4rts' OR phase='demis' OR phase='finale') AND done=4";
  $r = mysql_query($q) or die(mysql_error());
  while($match = mysql_fetch_array($r)){
    $id_team_A = $match['id_team_A'];
    $id_team_B = $match['id_team_B'];
    $q = "UPDATE prono_teams SET playing=1 WHERE id_team=$id_team_A OR id_team=$id_team_B";
    $result = mysql_query($q) or die(mysql_error());
  }
  echo "�quipes �limin�es : ".$result;
}

if(isset($_POST['update_match'])){
//Enregistrement du match
  $id_match = $_POST['update_match'];
  $score_A  = $_POST['score_A'];
  $score_B  = $_POST['score_B'];
  if(isset($_POST['penalties']))
	$penalties = 1;
	else
	  $penalties = 0;
  $r = mysql_query("SELECT * FROM prono_matchs
                    WHERE id_match=$id_match")
					or die(mysql_error());
  $match = mysql_fetch_array($r);
  $done = $match['done'];
  $done++;
  $q = "UPDATE prono_matchs 
	    SET score_A=$score_A, score_B=$score_B, done=$done, penalties=$penalties
	    WHERE id_match=$id_match";
  $result = mysql_query($q) or die(mysql_error());
  echo "Score du match enregistr� : ".$result."<br>";
//Mise � jour des paris
  $q="SELECT * FROM prono_paris WHERE id_match=$id_match";
  $r=mysql_query($q) or die(mysql_error());
  if($done==1){
    while ($pari = mysql_fetch_array($r)){
      $id_pari= $pari['id_pari'];
      $pari_A = $pari['pari_A'];
      $pari_B = $pari['pari_B'];
      $win    = $pari['win'];
      if(($score_A<$score_B && $win == 'B')
	   ||($score_B<$score_A && $win == 'A')
	   ||($score_B==$score_A && $win == 'even'))
	    $points = 2;
	    else
		  $points = 0;
	  if($score_A == $pari_A && $score_B == $pari_B)
	    $points = 3 ;
      $q = "UPDATE prono_paris
	        SET points=$points
	        WHERE id_pari=$id_pari";
      $result=mysql_query($q) or die(mysql_error());
    }
  }
    elseif($done==5){
	  $id_team_A = $match['id_team_A'];  //�quipes r�elles
	  $id_team_B = $match['id_team_B'];
	  $phase = $match['phase'];
      while($pari = mysql_fetch_array($r)){
        $id_pari= $pari['id_pari'];
        $pari_A = $pari['pari_A'];
        $pari_B = $pari['pari_B'];
        $win    = $pari['win'];
		$id_team_A_p = $pari['id_team_A'];
		$id_team_B_p = $pari['id_team_B'];
		$penalties_p = $pari['penalties'];
		
//Gestion des points en phase finales
        if((($score_A<$score_B && $win == 'B' && $id_team_B == $id_team_B_p)
	     ||($score_B<$score_A && $win == 'A' && $id_team_A == $id_team_A_p))
		 && ($penalties==0) && ($penalties_p==0)
		 && !($phase=='8emes' && ($id_team_A_p!=$id_team_A || $id_team_B_p!=$id_team_B))){
	      $points = 3;
	      if($id_team_A_p==$id_team_A && $id_team_B_p==$id_team_B){
		    if($phase!='8emes') $points +=2;
	        if($score_A == $pari_A && $score_B == $pari_B)
	          $points +=2;
	      }
		  if($phase=='finale'){
		    $points = 7;
		    if($id_team_A_p==$id_team_A && $id_team_B_p==$id_team_B){
		      $points +=5;
	          if($score_A == $pari_A && $score_B == $pari_B)
	            $points +=5;
		    }
		  }
	    }elseif(($penalties==1) && ($penalties_p==1)){
		   if(($win == 'A' && $id_team_A == $id_team_A_p)
			||($win == 'B' && $id_team_B == $id_team_B_p)){ // Si mon �quipe gagnante est bien dans le lot
			$points = 3;
	         if(($id_team_A_p==$id_team_A && $id_team_B_p==$id_team_B && $phase!='8emes') //Si j'ai les 2 bonnes �quipes OU le bon vainqueur
			  ||($score_A<$score_B && $win == 'B')||($score_B<$score_A && $win == 'A')){
				$points +=2;
				if($id_team_A_p==$id_team_A && $id_team_B_p==$id_team_B //Si j'ai tout
				 &&(($score_A<$score_B && $win == 'B')||($score_B<$score_A && $win == 'A'))
				 &&(min($score_A,$score_B)==min($pari_A,$pari_B)))
				  $points +=2;
	         }
	         if($phase=='finale'){
			   $points = 7;
			   if(($id_team_A_p==$id_team_A && $id_team_B_p==$id_team_B) //Si j'ai les 2 bonnes �quipes OU le bon vainqueur
			    ||($score_A<$score_B && $win == 'B')||($score_B<$score_A && $win == 'A')){
				 $points +=5;
				 if($id_team_A_p==$id_team_A && $id_team_B_p==$id_team_B //Si j'ai tout
				  &&(($score_A<$score_B && $win == 'B')||($score_B<$score_A && $win == 'A'))
				  &&(min($score_A,$score_B)==min($pari_A,$pari_B)))
				   $points +=5;
	            }
	          }
			}
	      }
	      else
		    $points = 0;
        
        $q = "UPDATE prono_paris
	          SET points=$points
	          WHERE id_pari=$id_pari";
        $result=mysql_query($q) or die(mysql_error());
      }
    }
	  else
        echo "GROS BUG DANS UPDATE_MATCH";
  echo "Paris pris en compte : ".$result."<br>";
}


if(isset($_POST['cancel_match'])){
//Annulation du match
  $id_match = $_POST['cancel_match'];
  $r = mysql_query("SELECT done,phase,poule FROM prono_matchs
  					WHERE id_match=$id_match") or die(mysql_error());
  $match = mysql_fetch_array($r);
  $done = $match['done'];
  $done--;
  $q = "UPDATE prono_matchs 
	    SET score_A=NULL, score_B=NULL, done=$done, penalties=0
	    WHERE id_match=$id_match";
  $result=mysql_query($q) or die(mysql_error());
  echo "Match annul� : ".$result;
//Annulation des paris : Pas besoin de toucher � la table
//Les points seront erron�s mais on n'en a pas besoin pour le reste
//dans la mesure o� dans les calculs de score on ne s�lectionne
//que les matchs jou�s
}

if(isset($_POST['compute_scores'])){
  $q = "SELECT id_user FROM prono_users";
  $r_users=mysql_query($q) or die(mysql_error());
  while ($user = mysql_fetch_array($r_users)){
	$id_user  = $user['id_user'];
	$score = 0;
	$bonus = 0;
	$q = "SELECT points,phase,prono_paris.id_match FROM prono_paris, prono_matchs
		  WHERE id_user=$id_user AND (done=1 OR done=5) AND prono_paris.id_match=prono_matchs.id_match";
    $r_paris=mysql_query($q) or die(mysql_error());
	while ($match = mysql_fetch_array($r_paris)){
	  $id_match = $match['id_match'];
	  $score += $match['points'];
	  if(($match['phase']=='poules' && $match['points']==3)
	   ||($match['points']>=5 && $match['phase']=='8emes') ||($match['points']>=7))
	  $bonus += 1;
	}
	mysql_query("UPDATE prono_users
				 SET score = $score, bonus =$bonus
				 WHERE id_user = $id_user") or die (mysql_error());
  }
  echo "Scores actualis�s";
}

if(isset($_POST['cancel_finales'])){
  $q = "UPDATE prono_teams SET playing=1";
  $r = mysql_query($q) or die(mysql_error());
    echo "�quipe r�int�gr�es :".$r."<br>";
  $q = "UPDATE prono_matchs, prono_paris
        SET done=3,prono_matchs.id_team_A=0, prono_matchs.id_team_B=0, prono_matchs.penalties=0, score_A=NULL, score_B=NULL, points=0
        WHERE (done=4 OR done=5)AND prono_matchs.id_match=prono_paris.id_match";
  $r = mysql_query($q) or die(mysql_error());
    echo "matchs et paris annul�s :".$r;
}

if(isset($_POST['eliminer_team'])){
  $id_team = $_POST['eliminer_team'];
  $q = "UPDATE prono_teams SET playing=0 WHERE id_team=$id_team";
  $r = mysql_query($q) or die(mysql_error());
  echo "�quipe �limin�e :".$r;
}

if(isset($_POST['log_as'])){
  $id_user = $_POST['log_as'];
  $_SESSION['current_ID'] = $id_user;
  echo "logged as : ".$id_user;
}

$_POST=array();
?>





<hr>









<table width="800" border="1" cellspacing='0' align="left">
  <tr><td colspan="2">
    <p align="center"><a href="main_page.php"> Retour � la page principale </a></p>
  </td></tr>
  <tr>
	<form action="" method="post">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>		</td>
	<td> Updater un match :
	<?php
  	  $r = mysql_query("SELECT * FROM prono_matchs WHERE done=0 OR done=4") or die(mysql_error());
  	  echo "<select name='update_match'>";
    	    while ($match = mysql_fetch_array($r)){
	          $qA = "SELECT name FROM prono_teams WHERE id_team='".$match['id_team_A']."'";
	          $qB = "SELECT name FROM prono_teams WHERE id_team='".$match['id_team_B']."'";
	          $nameA = mysql_query($qA) or die(mysql_error());
	          $nameA = mysql_fetch_array($nameA);
	          $nameB = mysql_query($qB) or die(mysql_error());;
	          $nameB = mysql_fetch_array($nameB);
	          $nameA = $nameA['name'];
	          $nameB = $nameB['name'];
	          $id_match=$match['id_match'];
	          $phase = $match['phase'];
	          $poule = $match['poule'];
      	      echo "<option value=$id_match>";
      	      if($phase!= 'poules')
				echo strtoupper($phase)." : ";
				else
				  echo $poule." : ";
      	      echo $nameA."  -  ".$nameB;
            }
  	  echo "</select>";
	  echo " score : ";
	  echo "<select name='score_A'>";
	  for($i=0; $i<11; $i++) echo "<option value=$i>".$i;
	  echo "</select>  -  ";
	  echo "<select name='score_B'>";
	  for($i=0; $i<11; $i++) echo "<option value=$i>".$i;
	  echo "</select> <input type='checkbox' name='penalties' value=1> tirs au but";
	?>
	</td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<input type="hidden" name="compute_scores" value="1">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>		</td>
	<td> Mettre � jour les scores </td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<td width="30"><p align="right"><input type="submit" value="OK"></p></td>
	<td> Changer la phase :<br>
	<?php
	$q = "SELECT value_int FROM prono_vars WHERE name='phase'";
	$r = mysql_query($q);
	$phase = mysql_fetch_array($r);
	echo "<input type='radio' name='change_phase' value='0' ";
	  if($phase['value_int']==0 || $phase['value_int']==NULL) echo "CHECKED";
	echo ">Pr�-poules (paris)<br>";
	echo "<input type='radio' name='change_phase' value='1' ";
	  if($phase['value_int']==1) echo "CHECKED";
	echo ">Poules (paris phases finales)<br>";
	echo "<input type='radio' name='change_phase' value='2' ";
	  if($phase['value_int']==2) echo "CHECKED";
	echo ">Phases finales (plus de paris)<br>";
	?>
	</td>
	</form>
  </tr>
  
  
  <tr><th colspan="2" align='left'>Updater les vainqueurs en phases finales</th></tr>

  <tr><td> </td><th bgcolor='CCCCFF' align='left'>Vainqueurs poules : ATTENTION OPERATION IRREVERSIBLE</th></tr>
<?php
  $q = "SELECT id_match,poule FROM prono_matchs WHERE phase='8emes' AND done=3 ORDER BY poule";
  $r_matchs = mysql_query($q) or die(mysql_error());
  $num_matchs = mysql_num_rows($r_matchs);
  while($match = mysql_fetch_array($r_matchs)){
	$poule = $match['poule'];
	$poule_2 = "";
	if($poule{0}=='1')
      $poule_2 .= '2';
	  else
	    $poule_2 .= '1';
	$p2 = $poule{1};
	$p2++;
	$poule_2 .= $p2;
	$p1 = $poule{1};
	$p2 = $poule_2{1};
	$q = "SELECT id_team,name FROM prono_teams WHERE poule='$p1'AND playing=1";
	$rA = mysql_query($q) or die(mysql_error());
	$q = "SELECT id_team,name FROM prono_teams WHERE poule='$p2'AND playing=1";
	$rB = mysql_query($q) or die(mysql_error());
           ?>
  <tr><form action="" method="post"><td rowspan='2'><input type='submit' value='OK'>
  		</td>
	  <td bgcolor='CCCCFF' align='left'><?php echo $poule;
	            echo "<input type='hidden' name='update_wins_poule' value='$poule' >";
	            if($num_matchs==1)
				  echo "<input type='hidden' name='eliminate_teams' value='1' >";
				echo "<select name='id_team_A'>";
	            while($team = mysql_fetch_array($rA)){
				  $id_team = $team['id_team'];
				  $name = $team['name'];
				  echo "<option value='$id_team'>".$name;
				}
	            echo "</select></td></tr><tr><td bgcolor='CCCCFF' align='left'>";
				echo $poule_2;
	            echo "<select name='id_team_B'>";
	            while($team = mysql_fetch_array($rB)){
				  $id_team = $team['id_team'];
				  $name = $team['name'];
				  echo "<option value='$id_team'>".$name;
				}
	            echo "</select></td></form></tr>";
  }
?>
  <tr><td> </td><th bgcolor='99FF99' align='left'>Vainqueurs huiti�mes : ATTENTION OPERATION IRREVERSIBLE</th></tr>
<?php
  $q = "SELECT id_match,poule FROM prono_matchs WHERE phase='4rts' AND done=3 ORDER BY poule";
  $r_matchs = mysql_query($q) or die(mysql_error());
  $num_matchs = mysql_num_rows($r_matchs);
  $q = "SELECT id_match FROM prono_matchs WHERE phase='8emes' AND done=3";
  $r_test = mysql_query($q) or die(mysql_error());
  $n_test = mysql_num_rows($r_test);
  if($n_test==0){
  while($match = mysql_fetch_array($r_matchs)){
	$poule = $match['poule'];
	$poule_q = $poule;
	$poule_2 = "";
	if($poule{0}=='1'){
      $poule_2 .= '1';
      $n = '2';
	  }
	  else{
	    $poule_2 .= '2';
        $n = '1';
	  }
	$poule .= $n;
	$letter = $poule{1};
	$letter++;
    $poule .= $letter;
	$letter++;
	$poule_2 = $poule_2.$letter.$n;
	$letter++;
    $poule_2 .= $letter;
	$p1 = $poule{1}; $p2 = $poule{3};
	$q = "SELECT id_team,name FROM prono_teams WHERE (poule='$p1' OR poule='$p2')AND playing=1";
	$rA = mysql_query($q) or die(mysql_error());

	$p1 = $poule_2{1}; $p2 = $poule_2{3};
	$q = "SELECT id_team,name FROM prono_teams WHERE (poule='$p1' OR poule='$p2')AND playing=1";
	$rB = mysql_query($q) or die(mysql_error());
           ?>
  <tr><form action="" method="post"><td rowspan='2'><input type='submit' value='OK'>
  		</td>
	  <td bgcolor='99FF99' align='left'><?php echo $poule;
	            echo "<input type='hidden' name='update_wins_8emes' value='$poule_q'>";
	            if($num_matchs==1)
				  echo "<input type='hidden' name='eliminate_teams' value='1' >";
				echo "<select name='id_team_A'>";
	            while($team = mysql_fetch_array($rA)){
				  $id_team = $team['id_team'];
				  $name = $team['name'];
				  echo "<option value='$id_team'>".$name;
				}
	            echo "</select></td></tr><tr><td bgcolor='99FF99' align='left'>";
				echo $poule_2;
	            echo "<select name='id_team_B'>";
	            while($team = mysql_fetch_array($rB)){
				  $id_team = $team['id_team'];
				  $name = $team['name'];
				  echo "<option value='$id_team'>".$name;
				}
	            echo "</select></td></form></tr>";
  }
  }
?>
  <tr><td> </td><th bgcolor='FFCC99' align='left'>Vainqueurs quarts : ATTENTION OPERATION IRREVERSIBLE</th></tr>
<?php
  $q = "SELECT id_match,poule FROM prono_matchs WHERE phase='demis' AND done=3 ORDER BY poule";
  $r_matchs = mysql_query($q) or die(mysql_error());
  $num_matchs = mysql_num_rows($r_matchs);
  $q = "SELECT id_match FROM prono_matchs WHERE phase='4rts' AND done=3";
  $r_test = mysql_query($q) or die(mysql_error());
  $n_test = mysql_num_rows($r_test);
  if($n_test==0){
  while($match = mysql_fetch_array($r_matchs) ){
	$poule = $match['poule'];
	$poule_q = $poule;
	if($poule{0}=='1'){
      $poule = '1A2B 1C2D';
      $poule_2 = '1E2F 1G2H';
	  }
	  else{
	    $poule = '2A1B 2C1D';
	    $poule_2 = '2E1F 2G1H';
	  }
	$p1 = $poule{1}; $p2 = $poule{3}; $p3 = $poule{6}; $p4 = $poule{8};
	$q = "SELECT id_team,name FROM prono_teams
		  WHERE (poule='$p1' OR poule='$p2' OR poule='$p3' OR poule='$p4')AND playing=1";
	$rA = mysql_query($q) or die(mysql_error());

	$p1 = $poule_2{1}; $p2 = $poule_2{3}; $p3 = $poule_2{6}; $p4 = $poule_2{8};
	$q = "SELECT id_team,name FROM prono_teams
	      WHERE (poule='$p1' OR poule='$p2' OR poule='$p3' OR poule='$p4')AND playing=1";
	$rB = mysql_query($q) or die(mysql_error());
           ?>
  <tr><form action="" method="post"><td rowspan='2'><input type='submit' value='OK'>
  		</td>
	  <td bgcolor='FFCC99' align='left'><?php echo $poule;
	            echo "<input type='hidden' name='update_wins_4rts' value='$poule_q'>";
	            if($num_matchs==1)
				  echo "<input type='hidden' name='eliminate_teams' value='1' >";
				echo "<select name='id_team_A'>";
	            while($team = mysql_fetch_array($rA)){
				  $id_team = $team['id_team'];
				  $name = $team['name'];
				  echo "<option value='$id_team'>".$name;
				}
	            echo "</select></td></tr><tr><td bgcolor='FFCC99' align='left'>";
				echo $poule_2;
	            echo "<select name='id_team_B'>";
	            while($team = mysql_fetch_array($rB)){
				  $id_team = $team['id_team'];
				  $name = $team['name'];
				  echo "<option value='$id_team'>".$name;
				}
	            echo "</select></td></form></tr>";
  }
  }
?>

  <tr><td> </td><th bgcolor='FFCCFF' align='left'>Vainqueurs demis : ATTENTION OPERATION IRREVERSIBLE</th></tr>

<?php
  $q = "SELECT id_match,poule FROM prono_matchs WHERE phase='finale' AND done=3";
  $r_matchs = mysql_query($q) or die(mysql_error());
  $num_matchs = mysql_num_rows($r_matchs);
  $q = "SELECT id_match FROM prono_matchs WHERE phase='demis' AND done=3";
  $r_test = mysql_query($q) or die(mysql_error());
  $n_test = mysql_num_rows($r_test);
  if($n_test==0){
  while($match = mysql_fetch_array($r_matchs)){
    $poule   = '1A 2B 1C 2D 1E 2F 1G 2H';
    $poule_2 = '2A 1B 2C 1D 2E 1F 2G 1H';
	$q = "SELECT id_team,name FROM prono_teams
		  WHERE playing=1";
	$rA= mysql_query($q) or die(mysql_error());
	$q = "SELECT id_team,name FROM prono_teams
		  WHERE playing=1";
	$rB= mysql_query($q) or die(mysql_error());
           ?>
  <tr><form action="" method="post"><td rowspan='2'><input type='submit' value='OK'>
  		</td>
	  <td bgcolor='FFCCFF' align='left'><?php echo $poule;
	            echo "<input type='hidden' name='update_wins_demis' value='1'>";
				echo "<select name='id_team_A'>";
	            while($team = mysql_fetch_array($rA)){
				  $id_team = $team['id_team'];
				  $name = $team['name'];
				  echo "<option value='$id_team'>".$name;
				}
	            echo "</select></td></tr><tr><td bgcolor='FFCCFF' align='left'>";
				echo $poule_2;
	            echo "<select name='id_team_B'>";
	            while($team = mysql_fetch_array($rB)){
				  $id_team = $team['id_team'];
				  $name = $team['name'];
				  echo "<option value='$id_team'>".$name;
				}
	            echo "</select></td></form></tr>";
  }
  }
?>

  <tr>
	<form action="" method="post">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>	</td>
	<td> Se logger en tant que : <select name="log_as">
	<?php
	$q = "SELECT id_user,login FROM prono_users";
	$r = mysql_query($q) or die(mysql_error());
	while($user=mysql_fetch_array($r)){
	  $id_user = $user['id_user'];
	  $login   = $user['login'];
	  echo "<OPTION value='$id_user'>".$login;
	}
	?>
	</select></td></form>
  </tr>
  <tr>
	<form action="" method="post">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>		</td>
	<td> Annuler le r�sultat d'un match :
	<?php
  	  $r = mysql_query("SELECT * FROM prono_matchs WHERE done=1 or done=5") or die(mysql_error());
  	  echo "<select name='cancel_match'>";
    	    while ($match = mysql_fetch_array($r)){
	          $qA = "SELECT name FROM prono_teams WHERE id_team='".$match['id_team_A']."'";
	          $qB = "SELECT name FROM prono_teams WHERE id_team='".$match['id_team_B']."'";
	          $nameA = mysql_query($qA) or die(mysql_error());
	          $nameA = mysql_fetch_array($nameA);
	          $nameB = mysql_query($qB) or die(mysql_error());;
	          $nameB = mysql_fetch_array($nameB);
	          $nameA = $nameA['name'];
	          $nameB = $nameB['name'];
	          $id_match = $match['id_match'];
	          $phase = $match['phase'];
	          $poule = $match['poule'];
      	      echo "<option value=$id_match>";
      	      if($phase!= 'poules')
				echo strtoupper($phase)." : ";
				else
				  echo $poule." : ";
      	      echo $nameA."  -  ".$nameB."   (";
	      echo $match['score_A']."-".$match['score_B'].")";
            }
  	  echo "</select>";
	?>
	</td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>	</td>
	<td> Eliminer une �quipe : <select name="eliminer_team">
	<?php
	$q = "SELECT id_team,name FROM prono_teams WHERE playing=1";
	$r = mysql_query($q) or die(mysql_error());
	while($team=mysql_fetch_array($r)){
	  $id_team = $team['id_team'];
	  $name    = $team['name'];
	  echo "<OPTION value='$id_team'>".$name;
	}
	?>
	</select></td></form>
  </tr>
  <tr>
	<form action="" method="post">
	<input type="hidden" name="cancel_finales" value="1">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>	</td>
	<td> Annuler TOUTES les phases finales </td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<input type="hidden" name="create_table_users" value="1">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>	</td>
	<td> Cr�er la table users </td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<input type="hidden" name="reset_scores_users" value="1">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>		</td>
	<td> Reseter les scores </td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>	</td>
	<td> Supprimer un user : <input type="text" name="del_user"></td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>		</td>
	<td> Rendre un user admin : <input type="text" name="adm_user"></td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>		</td>
	<td> Rendre un user sans privil�ge : <input type="text" name="unadm_user"></td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<input type="hidden" name="create_table_teams" value="1">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>		</td>
	<td> Cr�er la table des �quipes </td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<input type="hidden" name="create_table_matchs" value="1">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>		</td>
	<td> Cr�er la table des matchs </td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<input type="hidden" name="create_table_paris" value="1">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>		</td>
	<td> Cr�er la table des paris </td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<input type="hidden" name="create_table_messages" value="1">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>		</td>
	<td> Cr�er la table des messages </td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<input type="hidden" name="create_table_vars" value="1">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>		</td>
	<td> Cr�er la table des variables </td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<input type="hidden" name="create_final_matchs" value="1">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>		</td>
	<td> Cr�er les matchs de phases finales </td>
	</form>
  </tr>
  <tr><td colspan="2">
    <p align="center"><a href="fill_teams.php"> Gestion table �quipes </a></p>
  </td></tr>
  <tr><td colspan="2">
    <p align="center"><a href="fill_matchs.php"> Gestion table matchs </a></p>
  </td></tr>
  <tr><td colspan="2">
    <p align="center"><a href="main_page.php"> Retour � la page principale </a></p>
  </td></tr>
</table>

</body>
</html>
