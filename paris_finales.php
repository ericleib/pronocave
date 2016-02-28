<?php require_once("db_connect.php"); ?>
<?php session_start();

// Fonction pour récupérer la phase
function get_phase(){
	$q = "SELECT value_int FROM prono_vars WHERE name='phase'";
	$r = mysql_query($q) or die(mysql_error());
	return mysql_fetch_array($r);
}

// Fonction pour récupérer le nom d'une équipe
function get_name($id_team){
  $r = mysql_query("SELECT name FROM prono_teams WHERE id_team=$id_team")
                or die(mysql_error());
  $name = mysql_fetch_array($r);
  return $name['name'];
}

// Fonction pour écrire le formulaire de score
function print_form_score(){
  echo "<select name='pari_A'>";
  for($i=0; $i<11; $i++) echo "<option value=$i>".$i;
  echo "</select><br>-<br><select name='pari_B'>";
  for($i=0; $i<11; $i++) echo "<option value=$i>".$i;
  echo "</select><br>";
}

// Fonction pour écrire le formulaire caché pour modifier un pari
function print_form_mod($id_pari,$phase){
  echo "<form action='' method='post'>";
  echo "<input type='hidden' name='modify_bet' value='$id_pari'>";
  echo "<input type='hidden' name='modify_bet_phase' value='$phase'>";
  echo "<input type='submit' value='Modifier'></form>";
}

// Fonction pour écrire un paris (non éditable)
function print_pari($n_paris,$pari,$id_team_A_m,$id_team_B_m,$done){
	if($n_paris!=1) die("Plusieurs paris pour un match");
	$id_pari = $pari['id_pari'];
	$id_team_A = $pari['id_team_A'];
	$id_team_B = $pari['id_team_B'];
	$pari_A = $pari['pari_A'];
	$pari_B = $pari['pari_B'];
	$nameA = get_name($id_team_A);
	$nameB = get_name($id_team_B);
	if($done>3 && $id_team_A_m!=$id_team_A) // Si on s'est planté d'équipe
		echo "<font color='red'>".$nameA."</font>"; //...on affiche l'équipe en rouge
	else
		echo $nameA;
	echo"<br>".$pari_A."<br>-<br>".$pari_B."<br>";  // On affiche le prono
	if($done>3 && $id_team_B_m!=$id_team_B)
		echo "<font color='red'>".$nameB."</font><br>";
    else
		echo $nameB."<br>";
	return $id_pari;
}

// Fonction pour afficher un formulaire quand il n'y a pas de pari et qu'on peut parier
// Ou pour afficher "pas de pari" si on ne peut plus parier
function print_form_end($r1,$r2,$id_match,$phase,$periode){
  $pari_A = mysql_fetch_array($r1);
  $pari_B = mysql_fetch_array($r2);
  $id_team_A = $pari_A['id_team_'.$pari_A['win']]; //Récupérer l'id des vainqueurs
  $id_team_B = $pari_B['id_team_'.$pari_B['win']];
  $nameA = get_name($id_team_A);
  $nameB = get_name($id_team_B);
  echo $nameA."<br>";
  if($periode<=1){ // Si il n'existe pas de pari mais qu'on est en phase de pari on propose
    echo "<form action='' method='post'>";
    echo "<input type='hidden' name='send_bet' value='$id_match'>";
    echo "<input type='hidden' name='phase' value='$phase'>";
    echo "<input type='hidden' name='id_team_A' value='$id_team_A'>";
    echo "<input type='hidden' name='id_team_B' value='$id_team_B'>";
    print_form_score();
    echo $nameB."<br><input type='checkbox' name='penalties' value=1>tirs au but<br>";
    echo "<input type='submit' value='Envoyer'></form>";
  }
  else //Sinon tant pis, c'est trop tard !
    echo "pas de<br>pari<br>".$nameB;
}

// Détecter un intru
if ($_SESSION['login']){}
else {
	header("Location:index.php?out=intru");
}

// Mode LOG AS
$phase = get_phase();
if (isset($_GET['log_as']) && $phase['value_int']==2 ){
	$_SESSION['real_ID']=$_SESSION['current_ID'];
	$_SESSION['current_ID']=$_GET['log_as'];
}

// Récupérer un pari
if(isset($_POST['send_bet'])){
	$id_match = $_POST['send_bet'];
	$phase    = $_POST['phase'];
	$id_team_A = $_POST['id_team_A'];
	$id_team_B = $_POST['id_team_B'];
	$pari_A = $_POST['pari_A'];
	$pari_B = $_POST['pari_B'];
	if(isset($_POST['penalties']))
		$penalties = 1;
	else
		$penalties = 0;
	if($pari_A>$pari_B) $win='A';
	if($pari_B>$pari_A) $win='B';
	if($pari_A==$pari_B) header("Location:paris_finales.php?erreur=even#top"); // Erreur s'il y a égalité
	else{
		$id_user=$_SESSION['current_ID'];
		$q = "SELECT id_pari FROM prono_paris WHERE id_match=$id_match AND id_user=$id_user";
		$r = mysql_query($q) or die(mysql_error());
		if(mysql_num_rows($r)==0){  // Faire bien gaffe à ne pas enregistrer 2 paris pour le même match !
			$q = "INSERT INTO prono_paris(id_match,id_user,pari_A,pari_B,id_team_A,id_team_B,win,penalties)
			  VALUES($id_match,$id_user,$pari_A,$pari_B,$id_team_A,$id_team_B,'$win',$penalties)";
			mysql_query($q) or die(mysql_error());
		}
		header("Location:paris_finales.php#$phase");
	}
}

// Modifier (annuler) un pari
if(isset($_POST['modify_bet'])){
	$phase = $_POST['modify_bet_phase'];
	$id_pari  = $_POST['modify_bet'];
	$id_user  = $_SESSION['current_ID'];

	$test=0;
	if ($phase != 'finale'){
		$q = "SELECT id_match FROM prono_paris WHERE id_user=$id_user"; // On ne peut pas annuler un pari si des paris dépendent de son issue
		$r_paris = mysql_query($q) or die(mysql_error()); //Vérification qu'il n'y a pas de paris "au dessus"
		while($pari = mysql_fetch_array($r_paris)){
			$id_match = $pari['id_match'];
			$q = "SELECT id_match FROM prono_matchs WHERE id_match=$id_match AND (";
			if($phase=='8emes')
				$q .= "phase='4rts' OR phase='demis' OR phase='finale')";
			elseif($phase=='4rts')
				$q .= "phase='demis' OR phase='finale')";
			elseif($phase=='demis')
				$q .= "phase='finale')";
			else
				die("erreur !!");
			$r = mysql_query($q) or die(my_sql_error());
			if(mysql_num_rows($r))
				$test=1;
		}
	}
	if ($test==0){
		$q = "DELETE FROM prono_paris WHERE id_pari=$id_pari";
		mysql_query($q) or die(mysql_error());
		header("Location:paris_finales.php#$phase");
	}else
		header("Location:paris_finales.php?erreur=ordre");
}

?>

<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
       <title>Pronostics - phases finales</title>
       <meta httpequiv="ContentType" content="text/html; charset=windows-1252" />
<!--link rel="shortcut icon" href="BallonFoot.gif" type="image/x-icon"/-->
<!--link rel="icon" href="BallonFoot.gif" type="image/x-icon"/-->
	<link rel="stylesheet" type="text/css" href="style_div.css">
</head>
<body>

	<div class="header"> <img src="banniere.jpg" width=600 height=120> </div>
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

<?php
// Gestion des erreurs
if(isset($_GET['erreur']) && ($_GET['erreur'] == "ordre")){
    echo "<h1>Modifie tes pronos dans le bon ordre !</h1>";
	echo "<br>(d'abord la finale, plus les demis, puis les quarts, puis les huitièmes)<br>";
}
if(isset($_GET['erreur']) && ($_GET['erreur'] == "even")){
    echo "<a name='top'></a><h1>Pas d'égalité en phases finales !</h1>";
	echo "<br>Mets un point de plus à l'équipe gagnante et coche la case 'tirs au but'";
	echo "<br>(exemple: pour France-Italie 1-1, victoire de l'Italie aux tirs au but, entrer 1-2 et cocher 'tirs au but')<br>";
}

// Mode LOG AS
$phase = get_phase();
if(isset($_GET['log_as']) && $phase['value_int']==2){
	$id_user = $_SESSION['current_ID'];
	$q = mysql_fetch_array(mysql_query("SELECT login FROM prono_users WHERE id_user=$id_user"));
	echo "<h1> Pronostics de : ".$q['login']."</h1>";
}
?>

<br>
<a href='paris.php'>Phase de groupes</a>
<br>
<table width="600" border="1" cellspacing='0' align='center'>
<tr><th colspan='4'>Phases finales (modifiable jusqu'au 28/06 15:00)</th></tr>

<tr><th colspan='4' style='color:#6600CC'><a name="8emes">Huitièmes de finale</a></th></tr>
<?php
  $phase = get_phase();
  $id_user = $_SESSION['current_ID'];

  $q = "SELECT * FROM prono_matchs WHERE phase='8emes' AND done>2 AND done<=5  ORDER BY poule ASC";
  $r_matchs = mysql_query($q) or die(mysql_error());
  $cpt_col = 0;
  while($match = mysql_fetch_array($r_matchs)){ // Pour tous les matchs de 8emes
	$poule = $match['poule'];
	$id_match= $match['id_match'];
	$done = $match['done'];
	if($cpt_col==0)
		echo "<tr>";
	echo "<td align='center'>".$poule."<br>"; // Format : 1A,2A,1C,2C,1E,...
	
	$poule_2 = "";
	if($poule{0}=='1')
      $poule_2 .= '2';
	  else
	    $poule_2 .= '1';
	$p2 = $poule{1};
	$p2++;
	$poule_2 .= $p2;
	
    $q_paris = "SELECT * FROM prono_paris WHERE id_user=$id_user AND id_match=$id_match";
    $r_paris = mysql_query($q_paris);
    $pari	 = mysql_fetch_array($r_paris);
	$n_paris = mysql_num_rows($r_paris);
	
	if($n_paris>=1){    // Il existe un pari: on l'imprime
		$id_pari = print_pari($n_paris,$pari,$match['id_team_A'],$match['id_team_B'],$done);
		echo $poule_2."<br>";
		if ($pari['penalties']==1) echo "(tirs au but)<br>";
		if($phase['value_int']<=1) print_form_mod($id_pari,'8emes'); // Autoriser la modification si phase<=1
	}else{
		if($phase['value_int']>1)
			echo"Tu n'as<br>rien parié !<br>".$poule_2;
			
		else{       // Formulaire de pari pour les 8Emes

			$p1 = $poule{1};
			$p2 = $poule_2{1};
			$q = "SELECT id_team,name FROM prono_teams WHERE poule='$p1'AND playing=1";
			$rA = mysql_query($q) or die(mysql_error());
			$q = "SELECT id_team,name FROM prono_teams WHERE poule='$p2'AND playing=1";
			$rB = mysql_query($q) or die(mysql_error());
			
			echo "<form action='' method='post'>";
			echo "<input type='hidden' name='send_bet' value='$id_match'>";
			echo "<input type='hidden' name='phase' value='8emes'>";

			if($done==3){ // Si done == 3, on ne connait pas encore les équipes du match!
				echo "<select name='id_team_A'>";
				while($team = mysql_fetch_array($rA)){
				  $id_team = $team['id_team'];
				  $name = $team['name'];
				  echo "<option value='$id_team'>".$name;
				}
				echo "</select><br>";
			}else{
				$id_team_A=$match['id_team_A'];
				$id_team_B=$match['id_team_B'];
				$nameA = get_name($id_team_A);
				$nameB = get_name($id_team_B);
				echo "<input type='hidden' name='id_team_A' value=$id_team_A>";
				echo "<input type='hidden' name='id_team_B' value=$id_team_B>";
				echo $nameA."<br>";
			}
			
			print_form_score();
			if($done==3){
				echo "<select name='id_team_B'>";
				while($team = mysql_fetch_array($rB)){
					$id_team = $team['id_team'];
					$name = $team['name'];
					echo "<option value='$id_team'>".$name;
				}
				echo "</select>";
			}else
				echo $nameB;
			echo "<br>".$poule_2."<br><input type='checkbox' name='penalties' value=1>tirs au but<br>";
			echo "<input type='submit' value='Envoyer'></form>";
		}
	}
	echo "</td>";
	if($cpt_col==3){
	  echo "</tr>";
	  $cpt_col = -1;
	}
	$cpt_col++;
 }
?>

<tr><th colspan='4' style='color:#009900'><a name="4rts">Quarts de finale</a></th></tr>
<?php
  $q = "SELECT value_int FROM prono_vars WHERE name='phase'";
  $r = mysql_query($q) or die(mysql_error());
  $phase = mysql_fetch_array($r);

  $id_user = $_SESSION['current_ID'];
// On boucle sur les matchs réels pour être sur d'avoir le compte exact
  $q = "SELECT * FROM prono_matchs WHERE phase='4rts' AND done>2 AND done<=5  ORDER BY poule ASC";
  $r_matchs = mysql_query($q) or die(mysql_error());
  echo "<tr>";
  while($match=mysql_fetch_array($r_matchs)){
	$poule = $match['poule'];
	$id_match = $match['id_match'];
	$done = $match['done'];
	$poule_2 = $poule;
	$letter = $poule{1};
	$letter++;$letter++;
    $poule_2{1}=$letter;
	
	echo "<td align='center'>";

    $q_paris = "SELECT * FROM prono_paris WHERE id_user=$id_user AND id_match=$id_match";
    $r_paris = mysql_query($q_paris);
    $pari=mysql_fetch_array($r_paris);
	$n_paris = mysql_num_rows($r_paris);

	if($n_paris>=1){ // Un pari existe
	  $id_pari = print_pari($n_paris,$pari,$match['id_team_A'],$match['id_team_B'],$done);
	  if ($pari['penalties']==1) echo "(tirs au but)<br>";
	  if($phase['value_int']<=1) print_form_mod($id_pari,'4rts');
	}
	else{
// Lecture des phases antérieures :
	$q1 = "SELECT prono_matchs.id_match, win, prono_paris.id_team_A, prono_paris.id_team_B FROM prono_matchs,prono_paris
		   WHERE phase='8emes' AND poule='$poule' AND prono_matchs.id_match=prono_paris.id_match AND id_user=$id_user";
	$q2 = "SELECT prono_matchs.id_match, win, prono_paris.id_team_A, prono_paris.id_team_B FROM prono_matchs,prono_paris
		   WHERE phase='8emes' AND poule='$poule_2' AND prono_matchs.id_match=prono_paris.id_match AND id_user=$id_user";
    $r1 = mysql_query($q1) or die(mysql_error());
    $r2 = mysql_query($q2) or die(mysql_error());
    if (mysql_num_rows($r1)==0 || mysql_num_rows($r2)==0){
      echo "Besoin<br>des phases<br>antérieures";
    }
      else
        print_form_end($r1,$r2,$id_match,'4rts',$phase['value_int']);
    }
	echo "</td>";
	if($cpt_col==3)
	  echo "</tr>";
	$cpt_col++;
 }
?>

<tr></tr>
<tr><th colspan='4' style='color:#CC6600'><a name="demis">Demi-finales</a></th></tr>

<?php
	$phase = get_phase();

	$id_user = $_SESSION['current_ID'];

	// On boucle sur les matchs réels pour être sur d'avoir le compte exact
	$q = "SELECT * FROM prono_matchs WHERE phase='demis' AND done>2 AND done<=5  ORDER BY poule ASC";
	$r_matchs = mysql_query($q) or die(mysql_error());
	echo "<tr>";
	while($match=mysql_fetch_array($r_matchs)){
		$poule = $match['poule'];
		$id_match = $match['id_match'];
		$done = $match['done'];

		$poule_4rt_1 = $poule."A";
		$poule_4rt_2 = $poule."E";
		echo "<td colspan='2' align='center'>";

		$q_paris = "SELECT * FROM prono_paris WHERE id_user=$id_user AND id_match=$id_match";
		$r_paris = mysql_query($q_paris);
		$pari=mysql_fetch_array($r_paris);
		$n_paris = mysql_num_rows($r_paris);

		if($n_paris>=1){ // Un pari existe : on l'affiche (read only)
			$id_pari = print_pari($n_paris,$pari,$match['id_team_A'],$match['id_team_B'],$done);
			if ($pari['penalties']==1) echo "(tirs au but)<br>";
			if($phase['value_int']<=1) print_form_mod($id_pari,'demis');
			
		}else{
			// Lecture des phases antérieures :
			$q1 = "SELECT prono_matchs.id_match, win, prono_paris.id_team_A, prono_paris.id_team_B FROM prono_matchs,prono_paris
				   WHERE phase='4rts' AND poule='$poule_4rt_1' AND prono_matchs.id_match=prono_paris.id_match AND id_user=$id_user";
			$q2 = "SELECT prono_matchs.id_match, win, prono_paris.id_team_A, prono_paris.id_team_B FROM prono_matchs,prono_paris
				   WHERE phase='4rts' AND poule='$poule_4rt_2' AND prono_matchs.id_match=prono_paris.id_match AND id_user=$id_user";
			$r1 = mysql_query($q1) or die(mysql_error());
			$r2 = mysql_query($q2) or die(mysql_error());
			if (mysql_num_rows($r1)==0 || mysql_num_rows($r2)==0)
				echo "Besoin<br>des phases<br>antérieures";
			else
			    print_form_end($r1,$r2,$id_match,'demis',$phase['value_int']);
			    
		}
		
		echo "</td>";
		if($cpt_col==1)
		  echo "</tr>";
		$cpt_col++;
	}
?>

<tr></tr>
<tr><th colspan='4' style='color:#993399'><a name="finale">Finale</a></th></tr>

<?php
	$phase = get_phase();

	$id_user = $_SESSION['current_ID'];

	$q = "SELECT * FROM prono_matchs WHERE phase='finale' AND done>2 AND done<=5";
	$r_matchs = mysql_query($q) or die(mysql_error());
	$match=mysql_fetch_array($r_matchs);
	$id_match = $match['id_match'];
	$done = $match['done'];
	echo "<tr><td colspan='4' align='center'>";
  
    $q_paris = "SELECT * FROM prono_paris WHERE id_user=$id_user AND id_match=$id_match";
    $r_paris = mysql_query($q_paris);
    $pari=mysql_fetch_array($r_paris);
	$n_paris = mysql_num_rows($r_paris);

	if($n_paris>=1){ // Un pari existe
	  $id_pari = print_pari($n_paris,$pari,$match['id_team_A'],$match['id_team_B'],$done);
	  if ($pari['penalties']==1) echo "(tirs au but)<br>";
	  if($phase['value_int']<=1) print_form_mod($id_pari,'finale');
	  
	}else{
	// Lecture des phases antérieures :
		$q1 = "SELECT prono_matchs.id_match, win, prono_paris.id_team_A, prono_paris.id_team_B FROM prono_matchs,prono_paris
			   WHERE phase='demis' AND poule='1' AND prono_matchs.id_match=prono_paris.id_match AND id_user=$id_user";
		$q2 = "SELECT prono_matchs.id_match, win, prono_paris.id_team_A, prono_paris.id_team_B FROM prono_matchs,prono_paris
			   WHERE phase='demis' AND poule='2' AND prono_matchs.id_match=prono_paris.id_match AND id_user=$id_user";
	    $r1 = mysql_query($q1) or die(mysql_error());
	    $r2 = mysql_query($q2) or die(mysql_error());
	    if (mysql_num_rows($r1)==0 || mysql_num_rows($r2)==0){
	    	echo "Besoin<br>des phases<br>antérieures";
	    }else
	        print_form_end($r1,$r2,$id_match,'finale',$phase['value_int']);
	        
    }
	echo "</td></tr>";
?>

<?php
$phase = get_phase();
if(isset($_GET['log_as']) && $phase['value_int']==2) $_SESSION['current_ID']=$_SESSION['real_ID'];
?>

</table><br></div>
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
