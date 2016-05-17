<?php require_once("commons.php"); ?>
<?php

function print_match($match, $id_user, $phase, $final_name, $previous_final_name){		
	$id_match= $match['id_match'];
	$done = $match['done'];
	$paris = get_paris($id_match, $id_user);
	$id_team_A = $match['id_team_A'];
	$id_team_B = $match['id_team_B'];
	$team_A_label = $match['team_A'];
	$team_B_label = $match['team_B'];
		
	if(sizeof($paris)>=1){    // Il existe un pari: on l'imprime
		echo $team_A_label."<br>";
		$id_pari = print_pari(sizeof($paris),$paris[0],$id_team_A,$id_team_B,$done);
		echo $team_B_label."<br>";
		if ($paris[0]['penalties']==1) echo "(tirs au but)<br>";
		if($phase<=1) print_form_mod($id_pari,$final_name); // Autoriser la modification si phase<=1
		
	}elseif($final_name=='8emes'){
		if($phase>1)
			echo"<div class='alert alert-danger'>Tu n'as rien parié !</div>";
			
		else{       // Formulaire de pari pour les 8Emes
			
			echo "<form action='' method='post'>";
			echo "<input type='hidden' name='send_bet' value='$id_match'/>";
			echo "<input type='hidden' name='phase' value='".$final_name."'/>";

			echo $team_A_label."<br>";
			if($done==3){ // Si done == 3, on ne connait pas encore les équipes du match!
				print_select_teams($match['teams_A'], "A");
				print_form_score();
				print_select_teams($match['teams_B'], "B");
			}else{
				$nameA = get_name($id_team_A);
				$nameB = get_name($id_team_B);
				echo "<input type='hidden' name='id_team_A' value=$id_team_A/>";
				echo "<input type='hidden' name='id_team_B' value=$id_team_B/>";
				echo $nameA;
				print_form_score();
				echo $nameB;
			}
			
			echo "<br>".$team_B_label."<br>";
			echo "<input type='checkbox' name='penalties' value=1/>tirs au but<br>";
			echo "<input type='submit' class='btn btn-default bet-btn' value='Envoyer'/>";
			echo "</form>";
		}
		
    }else{
		$r1 = get_paris_for_final_match($id_user, $previous_final_name, $match['poule_A']);
		$r2 = get_paris_for_final_match($id_user, $previous_final_name, $match['poule_B']);
		if (sizeof($r1)==0 || sizeof($r2)==0){
			echo "<div class='alert alert-warning'>Remplir les phases antérieures !</div>";
		}else
			print_form_end($r1[0],$r2[0],$id_match,$final_name,$phase, $team_A_label, $team_B_label);
    }
}

// Fonction pour écrire le formulaire de score
function print_form_score(){
  echo "<br><select name='pari_A'>";
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
  echo "<input type='submit' class='btn btn-default bet-btn' value='Modifier'></form>";
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
function print_form_end($pari_A,$pari_B,$id_match,$phase,$periode,$teamA,$teamB){
  $id_team_A = $pari_A['id_team_'.$pari_A['win']]; //Récupérer l'id des vainqueurs
  $id_team_B = $pari_B['id_team_'.$pari_B['win']];
  $nameA = get_name($id_team_A);
  $nameB = get_name($id_team_B);
  if($periode<=1){ // Si il n'existe pas de pari mais qu'on est en phase de pari on propose
    echo "<form action='' method='post'>";
    echo "<input type='hidden' name='send_bet' value='$id_match'>";
    echo "<input type='hidden' name='phase' value='$phase'>";
    echo "<input type='hidden' name='id_team_A' value='$id_team_A'>";
    echo "<input type='hidden' name='id_team_B' value='$id_team_B'>";
	echo $teamA."<br>".$nameA;
    print_form_score();
    echo $nameB."<br>".$teamB."<br>";
	echo "<input type='checkbox' name='penalties' value=1>tirs au but<br>";
    echo "<input type='submit' class='btn btn-default bet-btn' value='Envoyer'></form>";
  }else //Sinon tant pis, c'est trop tard !
	echo "<div class='alert alert-danger'>Pas de pari !</div>";
}



kick_out_intruders(False);
log_as($PHASE_FINAL);

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

<?php print_html_header("Pronostics - Phase Finale", True); ?>

<h1>Phase finale</h1>
<h3>(modifiable jusqu'au 25/06 15:00)</h3>

<div class="col-md-8 col-sm-10 col-md-offset-2 col-sm-offset-1">

<?php
// Gestion des erreurs
if(isset($_GET['erreur']) && ($_GET['erreur'] == "ordre")){
    echo "<div class='alert alert-danger'><strong>Modifie tes pronos dans le bon ordre !</strong>";
	echo "<br>(d'abord la finale, plus les demis, puis les quarts, puis les huitièmes)</div>";
}
if(isset($_GET['erreur']) && ($_GET['erreur'] == "even")){
    echo "<div class='alert alert-danger'><strong>Pas d'égalité en phase finales !</strong>";
	echo "<br>Mets un point de plus à l'équipe gagnante et coche la case 'tirs au but'";
	echo "<br>(exemple: pour France-Italie 1-1, victoire de l'Italie aux tirs au but, entrer 1-2 et cocher 'tirs au but')</div>";
}

// Mode LOG AS
if(isset($_GET['log_as']) && get_phase()==2){
	echo "<h3> Pronostics de : ".get_user_name()."</h3>";
}
?>


<h3 style='color:#6600CC'>Huitièmes de finale</h3>

<?php
  $phase = get_phase();
  $id_user = $_SESSION['current_ID'];

  foreach(get_matchs_8emes() as $match){
	echo "<div class=\"col-md-3 col-sm-4 col-xs-6 margin_down bet\">";
	print_match($match, $id_user, $phase, '8emes', '');
	echo "</div>";
  }
?>

<div class='clearfix'></div>
<h3 style='color:#009900'>Quarts de finale</h3>

<?php
  foreach(get_matchs_4rts() as $match){
	echo "<div class=\"col-xs-6 margin_down bet\">";
	print_match($match, $id_user, $phase, '4rts', '8emes');
	echo "</div>";
  }
?>

<div class='clearfix'></div>
<h3 style='color:#CC6600'>Demi-finales</h3>

<?php
  foreach(get_matchs_demis() as $match){
	echo "<div class=\"col-xs-6 margin_down bet\">";
	print_match($match, $id_user, $phase, 'demis', '4rts');
	echo "</div>";
  }
?>

<div class='clearfix'></div>
<h3 style='color:#993399'>Finale</h3>

<div class="margin_down col-xs-12 bet">
<?php
  print_match(get_match_finale(), $id_user, $phase, 'finale', 'demis');
?>
</div>
</div>
<?php un_log_as($PHASE_FINAL); ?>
<?php print_html_footer(True); ?>
