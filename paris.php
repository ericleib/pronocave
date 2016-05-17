<?php require_once("commons.php"); ?>
<?php
kick_out_intruders(False);
log_as($PHASE_GROUP);


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

<?php print_html_header("Pronostics - Phase de Groupes", True); ?>

<h1>Phase de Groupes</h1>
<h3>(modifiable jusqu'au 10/06 15:00)</h3>

<?php
if(isset($_GET['log_as']) && get_phase()==1){
  echo "<h3> Pronostics de : ".get_user_name()."</h3>";
}
?>

<div class=" col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 margin_down table-responsive">
<table  class="table margin_down">
<thead>
  <tr><th colspan='4' style="color:red">Attention: 'Envoyer' enregistre les pronos pour un seul groupe:<br>Les autres ne seront pas pris en compte. <br>'Modifier' remet à zero les pronos pour le groupe.</th></tr>
</thead>
<tbody>
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
 		    <td rowspan='6' style='vertical-align: middle;'>Groupe ".$poule."</td><td style='border-right:0px; text-align:right; padding-right:4px;'><a name=$poule></a>";
      $q= "SELECT id_pari FROM prono_paris
	   	   WHERE id_user=$id_user AND id_match=$id_match";
	  $r_pari = mysql_query($q) or die(mysql_error());
      $n_pari = mysql_num_rows($r_pari);
    }else
	  echo "<tr><td style='border-right:0px; text-align:right; padding-right:4px;'>";
	  
    if($n_pari){    // Il y a un paris pour ce match (test du premier match de la poule)
		$q= "SELECT * FROM prono_paris
		   WHERE id_user=$id_user AND id_match=$id_match";
		$r_pari = mysql_query($q) or die(mysql_error());
		$pari = mysql_fetch_array($r_pari);
		$id_pari = $pari['id_pari'];
		$pari_A = $pari['pari_A'];
		$pari_B = $pari['pari_B'];
		echo $nameA."&nbsp;&nbsp;&nbsp;<b>".$pari_A."</b> ";
		echo "</td><td style='border-left:0px; text-align:left; padding-left:4px;'>";
		echo "<b> ".$pari_B."</b>&nbsp;&nbsp;&nbsp;".$nameB;
		echo "<input type='hidden' name='id_pari_$cpt_poule' value='$id_pari'></td>";
		$text_button = 'Modifier';
		$php_post = 'modify_poule_bet';
		
    }else{      // Il n'y en a pas
	    echo $nameA."  <select name='pari_A_$cpt_poule'>";
	    for($i=0; $i<11; $i++) echo "<option value=$i>".$i;
	    echo "</select> </td><td style='text-align:left; padding-left:4px;'> <select name='pari_B_$cpt_poule'>";
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
	    echo "<td rowspan='6' style='vertical-align: middle;'>
	          <input type='hidden' name='$php_post' value=$poule>
	          <input type='submit' class='btn btn-default' value='$text_button'></td>";
    }
    echo "</tr>";
    $cpt_poule++;
}
  
?>
</tbody></table></div>
<?php un_log_as($PHASE_GROUP); ?>
<?php print_html_footer(True); ?>
