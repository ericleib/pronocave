<?php require_once("db_connect.php"); ?>
<?php session_start();

function get_name($id_team){
  $r = mysql_query("SELECT name FROM prono_teams WHERE id_team=$id_team")
                or die(mysql_error());
  $name = mysql_fetch_array($r);
  return $name['name'];
}

if ($_SESSION['login']){}
else {
	header("Location:index.php?out=intru");
}

// LOG AS: remplacer l'ID en mode phase==1 (si je ne m'abuse, après la fin des paris poule)
$q = "SELECT value_int FROM prono_vars WHERE name='phase'";
$phase = mysql_fetch_array(mysql_query($q));
if (isset($_GET['log_as']) && $phase['value_int']==1 ){
	$_SESSION['real_ID']=$_SESSION['current_ID'];
	$_SESSION['current_ID']=$_GET['log_as'];
}

//
if(isset($_POST['modify_poule_bet'])){  // On a cliqué sur modfier
	  $q = "SELECT value_int FROM prono_vars WHERE name='phase'";
	  $r = mysql_query($q) or die(mysql_error());
	  $phase = mysql_fetch_array($r);
	  if($phase['value_int']==0){       // Si on est en phase de paris
		    $poule   = $_POST['modify_poule_bet'];
		    for($i=0; $i<=5; $i++)  $id_pari[$i] = $_POST['id_pari_'.$i]; // Récupérer les paris modifiés
		    $q = "DELETE FROM prono_paris
		        WHERE id_pari='$id_pari[0]'
		           OR id_pari='$id_pari[1]'
				   OR id_pari='$id_pari[2]'
				   OR id_pari='$id_pari[3]'
				   OR id_pari='$id_pari[4]'
				   OR id_pari='$id_pari[5]'";   // Les supprimer de la table des paris
		    $r = mysql_query($q) or die(mysql_error());
		    header("Location:paris.php#$poule");    // Pointer sur la bonne poule
	  }
}

if(isset($_POST['set_poule_bet'])){    // On a cliqué sur "OK"
	$q = "SELECT value_int FROM prono_vars WHERE name='phase'";
	$r = mysql_query($q) or die(mysql_error());
	$phase = mysql_fetch_array($r);
	
	if($phase['value_int']==0){     // Si on est bien en phase "paris poule"
		$id_user = $_SESSION['current_ID'];
		$id_match= $_POST['id_match_0'];
		$poule   = $_POST['set_poule_bet'];
		$q= "SELECT id_match FROM prono_paris
			WHERE id_match=$id_match AND id_user=$id_user"; //On sélection le premier match modifié
		$r= mysql_query($q) or die(mysql_error()); // Gestion double envoi de formulaire
		if(mysql_num_rows($r)==0){      // Si il n'y avait pas déjà de paris pour ce match...
			for($i=0; $i<=5; $i++){
				$pari_A = $_POST['pari_A_'.$i];     // Alors, pour chaque match, on insère un paris dans la table
				$pari_B = $_POST['pari_B_'.$i];
				$id_match= $_POST['id_match_'.$i];
				if($pari_A<$pari_B)
					$win='B';
				elseif($pari_A>$pari_B)
					$win='A';
				else
				    $win='even';
				$q = "INSERT INTO prono_paris(id_match,id_user,pari_A,pari_B,win)
				    VALUES($id_match,$id_user,$pari_A,$pari_B,'$win')";
				mysql_query($q) or die(mysql_error());
			}
		}
		header("Location:paris.php#$poule");
	}
}
$_POST=array();

?>

<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
       <title>Pronostics - Phase de groupes</title>
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

<?php
$q = "SELECT value_int FROM prono_vars WHERE name='phase'";
$phase = mysql_fetch_array(mysql_query($q));
if(isset($_GET['log_as']) && $phase['value_int']==1){
  $id_user = $_SESSION['current_ID'];
  $q = mysql_fetch_array(mysql_query("SELECT login FROM prono_users WHERE id_user=$id_user"));
  echo "<h1> Pronostics de : ".$q['login']."</h1>";
}
?>

<br>
<a href='paris_finales.php'>Phases finales</a>
<br>
<table width="600" border="1" cellspacing='0' align='center'>
<tr><th colspan='4'>Phase de groupes (modifiable jusqu'au 07/06 15:00)</th></tr>
<tr><th colspan='4' style="color:red">Entrer les résultats groupe par groupe !</th></tr>
<?php
$q = "SELECT * FROM prono_matchs
	WHERE phase='poules' ORDER BY poule ASC, date ASC";
$r_matchs=mysql_query($q) or die(mysql_error());
$cpt_poule=0;
$id_user = $_SESSION['current_ID'];
while ($match = mysql_fetch_array($r_matchs)){  // Pour chaque match
    $id_match = $match['id_match'];
    $id_team_A= $match['id_team_A'];
    $id_team_B= $match['id_team_B'];
    $poule    = $match['poule'];
    $nameA = get_name($id_team_A);
    $nameB = get_name($id_team_B);
    if($cpt_poule==0){
      echo "<tr><form action='' method='post'>
 		    <td width='50' rowspan='6'>Groupe ".$poule."</td><td align='right' style='border-right:0px'><a name=$poule></a>";
      $q= "SELECT id_pari FROM prono_paris
	   	   WHERE id_user=$id_user AND id_match=$id_match";
	  $r_pari = mysql_query($q) or die(mysql_error());
      $n_pari = mysql_num_rows($r_pari);
    }else
	  echo "<tr><td align='right' style='border-right:0px'>";
	  
    if($n_pari){    // Il y a un paris pour ce match (test du premier match de la poule)
		$q= "SELECT * FROM prono_paris
		   WHERE id_user=$id_user AND id_match=$id_match";
		$r_pari = mysql_query($q) or die(mysql_error());
		$pari = mysql_fetch_array($r_pari);
		$id_pari = $pari['id_pari'];
		$pari_A = $pari['pari_A'];
		$pari_B = $pari['pari_B'];
		echo $nameA."   <b>".$pari_A."</b> ";
		echo "</td><td align='left' style='border-left:0px'>";
		echo "<b> ".$pari_B."</b>   ".$nameB;
		echo "<input type='hidden' name='id_pari_$cpt_poule' value='$id_pari'></td>";
		$text_button = 'Modifier';
		$php_post = 'modify_poule_bet';
		
    }else{      // Il n'y en a pas
	    echo $nameA."  <select name='pari_A_$cpt_poule'>";
	    for($i=0; $i<11; $i++) echo "<option value=$i>".$i;
	    echo "</select> </td><td align='left'> <select name='pari_B_$cpt_poule'>";
	    for($i=0; $i<11; $i++) echo "<option value=$i>".$i;
	    echo "</select>  ".$nameB;
	    echo "<input type='hidden' name='id_match_$cpt_poule' value='$id_match'></td>";
	    $text_button = 'Envoyer';
	    $php_post = 'set_poule_bet';
	}
	
    if($cpt_poule==5){ // 5 = 0.5*N*(N-1)-1 avec N= nombre d'équipes/poule
		$cpt_poule=-1;
		echo "</form>";
    }elseif($cpt_poule==0){
	    echo "<td width='40' rowspan='6'>
	          <input type='hidden' name='$php_post' value=$poule>
	          <input type='submit' value='$text_button'></td>";
    }
    echo "</tr>";
    $cpt_poule++;
}
  
?>

<?php       //Gestion LOG AS
$q = "SELECT value_int FROM prono_vars WHERE name='phase'";
$phase = mysql_fetch_array(mysql_query($q));
if(isset($_GET['log_as']) && $phase['value_int']==1) $_SESSION['current_ID']=$_SESSION['real_ID'];
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
