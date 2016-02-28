<?php require_once("db_connect.php"); ?>
<?php session_start();

// Fonction pour récupérer le nom d'une équipe
function get_name($id_team){
  $r = mysql_query("SELECT name FROM prono_teams WHERE id_team=$id_team")
                or die(mysql_error());
  $name = mysql_fetch_array($r);
  return $name['name'];
}

if ($_SESSION['login']){}
else {
	header("Location:index.php?out=intru");
}?>

<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
       <title>Matchs</title>
       <meta httpequiv="ContentType" content="text/html; charset=windows-1252" />
       
<link rel="shortcut icon" href="BallonFoot.gif" type="image/x-icon"/>
<link rel="icon" href="BallonFoot.gif" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" href="style_div.css">
</head>
<body>

	<div class="header"> <img src="banniere.jpg" width=410 height=120> </div>
	<div class="barre">
	<ul id="boutons">
			<li><a href="main_page.php">Accueil</a></li>
			<li><a href="regles.php">Règles</a></li>
			<li><a href="paris.php">Pronostics</a></li>
			<li><a href="matchs.php">Matchs</a></li>
			<li><a href='index.php?out=deco'>Déconnexion</a></li>
	</ul>
	</div>
	<div class="contenu">
	

<h1> Matchs terminés</h1>
<table width='600' border='1' cellspacing='0' align='center'>
<tr><th width='50'> Date </th><th> Matchs </th><th width='50'> Score </th><th width='60'> Tendance* </th>
	<th width='50'> Total<br>points </th><th width='50'>Mon<br>prono</th><th width='50'>Mes<br>points</th></tr>
<?php
$id_user = $_SESSION['current_ID'];
$q = "SELECT * FROM prono_matchs WHERE done=1 OR done=5 ORDER BY date DESC";
$r_matchs = mysql_query($q) or die(mysql_error());
while($match = mysql_fetch_array($r_matchs)){   // Pour chaque match terminé
    $id_match = $match['id_match'];
    $id_team_A= $match['id_team_A'];
    $id_team_B= $match['id_team_B'];
    $score_A  = $match['score_A'];
    $score_B  = $match['score_B'];
    $date     = $match['date'];
    $phase     = $match['phase'];
    $date     = strtotime($date);
    $date     = date('j/m',$date);
    $nameA = get_name($id_team_A);
    $nameB = get_name($id_team_B);
    $cote_A=0;
    $cote_B=0;
    $points=0;
    $q = "SELECT * FROM prono_paris WHERE id_match=$id_match";
    $r = mysql_query($q)or die(mysql_error());
    $points_user = 0;
    $penalties = 0; 	// Attention : différent de $match['penalties']
    while($pari = mysql_fetch_array($r)){   // Pour chaque pari sur ce match
		$points += $pari['points'];//rajouter stats !
		if($id_user==$pari['id_user']) $points_user = $pari['points'];
		if($match['done']==1 || ($id_team_A==$pari['id_team_A'] && $id_team_B==$pari['id_team_B'])){
			$win = $pari['win'];
			if ($win=='A')
				$cote_A++;
			elseif($win=='B')
			    $cote_B++;
			else{
				$cote_A++;
				$cote_B++;
			}
			if($id_user==$pari['id_user']){
				$pari_user_A = $pari['pari_A'];
				$pari_user_B = $pari['pari_B'];
				$penalties   = $pari['penalties'];
			}
		}
    }
    if($cote_A != 0 || $cote_B != 0){
		if($cote_A == 0)
			$cote_B='&#8734';
		elseif($cote_B == 0)
			$cote_A='&#8734';
		elseif($cote_A>=$cote_B){
			$cote_A = round($cote_A/$cote_B,2);
			$cote_B = 1;}
		else{
			$cote_B = round($cote_B/$cote_A,2);
			$cote_A = 1;
		}
    }
    echo "<tr><td align='center'>".$date."</td>";
    echo "<td align='center'";
    	// Colorer les lignes du tableau
	    if ($phase=='4rts') echo "style='color:#009900'";
	    if ($phase=='demis') echo "style='color:#CC6600'";
	    if ($phase=='finale') echo "style='color:#993399'";
	echo ">";
	
	// Mettre en gras l'équipe gagnante
	if($score_A>=$score_B) echo "<b>".$nameA."</b>";
	else echo $nameA;
	echo "  -  ";
	if($score_B>=$score_A) echo "<b>".$nameB."</b>";
	  else echo $nameB;
	echo "</td>";
	
	// Afficher le score
	if($match['penalties']==1){     // En cas de péno, le score est juste là pour stocker le vainqueur
		$score_A= min($score_A,$score_B);
		$score_B= $score_A;
	}
    echo "<td align='center'>".$score_A."  -  ".$score_B."</td>";
    echo "<td align='center'>".$cote_A."  :  ".$cote_B."</td>";
    echo "<td align='center'>".$points."</td>";
    
    // Afficher les paris
	if(isset($pari_user_A)){
		if($penalties==1){
			$pari_user_A= min($pari_user_A,$pari_user_B);
			$pari_user_B= $pari_user_A;
		}
		echo "<th align='center' style='color:red'>".$pari_user_A."  -  ".$pari_user_B."</th>";
		unset($pari_user_A);
		unset($pari_user_B);
    }else
        echo "<td align='center' style='color:red'><b>X</b></td>";

	echo "<th align='center' style='color:red'>";
	if($points_user>0) echo "+".$points_user;
		else echo "-";
	echo "</th></tr>";
}
?>
</table>
	
<h1> Matchs à venir</h1>
<table width='600' border='1' cellspacing='0' align='center'>
<tr><th width='50'> Date </th><th> Matchs</th><th width='60'> Tendance* </th><th width='50'>Mon<br>prono</th> </tr>
<?php
$id_user = $_SESSION['current_ID'];
$q = "SELECT * FROM prono_matchs WHERE done=0 OR done=4 ORDER BY date ASC";
$r_matchs = mysql_query($q) or die(mysql_error());
while($match = mysql_fetch_array($r_matchs)){
    $id_match = $match['id_match'];
    $id_team_A= $match['id_team_A'];
    $id_team_B= $match['id_team_B'];
    $done     = $match['done'];
    $date     = $match['date'];
    $phase    = $match['phase'];
    $date     = strtotime($date);
    $date     = date('j/m',$date);
    $nameA = get_name($id_team_A);
    $nameB = get_name($id_team_B);
    $cote_A=0;
    $cote_B=0;
    if ($done==0)   // Phase de poule
    	$q = "SELECT win,pari_A,pari_B,id_user,penalties FROM prono_paris WHERE id_match=$id_match";
    else            // Phases finales done =4 -> On ne sélectionne le paris que si ce sont les bonne équipes !
        $q = "SELECT win,pari_A,pari_B,id_user,penalties FROM prono_paris WHERE id_match=$id_match
		      AND id_team_A=$id_team_A AND id_team_B=$id_team_B";
	$r = mysql_query($q)or die(mysql_error());
	$penalties = 0;
    while($pari = mysql_fetch_array($r)){   // On parcourt tous les paris pour calculer la cote
		$win = $pari['win'];
		if ($win=='A')
			$cote_A++;
		elseif($win=='B')
			$cote_B++;
		else{
			$cote_A++;
			$cote_B++;}
		if($id_user==$pari['id_user']){     // On garde en mémoire le pari de l'utilisateur, si il existe !
			$pari_user_A = $pari['pari_A'];
			$pari_user_B = $pari['pari_B'];
			$penalties = $pari['penalties'];
		}
    }
    if($cote_A != 0 || $cote_B != 0){
		if($cote_A == 0)
			$cote_B='&#8734';
		elseif($cote_B == 0)
			$cote_A='&#8734';
		elseif($cote_A>=$cote_B){
			$cote_A = round($cote_A/$cote_B,2);
			$cote_B = 1;}
		else{
			$cote_B = round($cote_B/$cote_A,2);
			$cote_A = 1;
		}
    }

    echo "<tr><td align='center'>".$date."</td>";
    echo "<td align='center' ";
	    if ($phase=='4rts') echo "style='color:#009900'";
	    if ($phase=='demis') echo "style='color:#CC6600'";
	    if ($phase=='finale') echo "style='color:#993399'";
	echo ">".$nameA."  -  ".$nameB."</td>";
	
    echo "<td align='center'>".$cote_A."  :  ".$cote_B."</td>";
	if(isset($pari_user_A)){
		if($penalties==1){
			$pari_user_A = min($pari_user_A,$pari_user_B);
			$pari_user_B = $pari_user_A;
		}
		echo "<th align='center' style='color:red'>".$pari_user_A."  -  ".$pari_user_B."</th></tr>";
		unset($pari_user_A);
		unset($pari_user_B);
	}else
		echo "<td align='center' style='color:red'><b>X</b></td></tr>";
}
?>
</table>
<br>
<div class ="texte">
<font color=009900>Quarts de finale</font> -
 <font color=CC6600>Demi-finales</font> -
 <font color=993399>Finale</font><br><br>
<u><b>tendance</b></u>: La tendance indique quelle équipe est donnée gagnante.<br>
"1:5" indique que l'équipe B est donnée 5 fois plus gagnante que l'équipe A.<br>
"1:1" indique que les pronostics sont équilibrés, ou que le match nul est beaucoup pronostiqué.<br><br>

</div>
</div>
    <div class="footer">
    	<ul id="boutons">
			<li><a href="main_page.php">Accueil</a></li>
			<li><a href="regles.php">Règles</a></li>
			<li><a href="paris.php">Pronostics</a></li>
			<li><a href="matchs.php">Matchs</a></li>
			<li><a href='index.php?out=deco'>Déconnexion</a></li>
	</ul>
	</div>
</body>
</html>
