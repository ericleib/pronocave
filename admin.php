<?php require_once("commons.php"); ?>
<?php kick_out_intruders(True); ?>

<?php print_html_header("Administration", False); ?>

<?php

function print_form_matchs_finale($matchs, $color, $form_name){
	foreach($matchs as $match){
		if($match['done']==3){
			$poule = $match['poule'];
			echo "<tr><form action='' method='post'><td rowspan='2'><input type='submit' value='OK'></td>";
			echo "<td bgcolor='$color' style='text-align: left;'>";
			echo "<input type='hidden' name='$form_name' value='$poule' >";		
			echo $match['team_A'];	
			print_select_teams($match['teams_A'], "A");
			echo "</td></tr>";
			
			echo "<tr><td bgcolor='$color' style='text-align: left;'>";
			echo $match['team_B'];			
			print_select_teams($match['teams_B'], "B");			
			echo "</td></form></tr>";
		}
	}
}

function update_winner($phase, $phase_next){
  $poule = $_POST["update_wins_".$phase];
  $id_team_A= $_POST['id_team_A'];
  $id_team_B= $_POST['id_team_B'];
  $q = "UPDATE prono_matchs SET id_team_A=$id_team_A,id_team_B=$id_team_B,done=4
        WHERE poule='$poule' AND done=3 AND phase='$phase_next'";
  $result=mysql_query($q) or die(mysql_error());
  echo "match de $phase_next enregistr�: ".$result;
}

// Cr�er la table users
if(isset($_POST['create_table_users'])){
  $result = create_table_users();
  echo "Table cr��e : ".$result;
}

// Mettre tous les scores et bonus � 0
if(isset($_POST['reset_scores_users'])){
  $q = "UPDATE prono_users SET score=0, bonus=0";
  $result=mysql_query($q) or die(mysql_error());
  echo "Scores mis � 0 : ".$result;
}

// Cr�er la table team
if(isset($_POST['create_table_teams'])){
  $result = create_table_teams();
  echo "Table cr��e : ".$result;
}

// Cr�er la table matchs
if(isset($_POST['create_table_matchs'])){
  $result = create_table_matchs();
  echo "Table cr��e : ".$result;
}

// Cr�er la table paris
if(isset($_POST['create_table_paris'])){
  $result = create_table_paris();
  echo "Table cr��e : ".$result;
}

// Cr�er la table messages
if(isset($_POST['create_table_messages'])){
  $result = create_table_messages();
  echo "Table cr��e : ".$result;
}

// Cr�er la table vars
if(isset($_POST['create_table_vars'])){
  $result = create_table_vars();
  echo "Table cr��e : ".$result;
}

// Cr�er la table des matchs de phases finales
if(isset($_POST['create_final_matchs'])){
  $result = create_finals();
  echo "Matchs cr��s : ".$result;
}

// Changer de phase
if(isset($_POST['change_phase'])){
  $result = set_phase($_POST['change_phase']);
  echo "Phase chang�e: ".$result;
}

// Mettre � jour des vainqueurs de poules
if(isset($_POST['update_wins_poule'])){
  update_winner('poule','8emes');
}

// Mettre � jour des vainqueurs de 8emes
if(isset($_POST['update_wins_8emes'])){
  update_winner('8emes','4rts');
}

// Mettre � jour des vainqueurs de 4rt
if(isset($_POST['update_wins_4rts'])){
  update_winner('4rts','demis');
}

// Mettre � jour des vainqueurs de demi
if(isset($_POST['update_wins_demis'])){
  update_winner('demis','finale');
}

// Poster un r�sultat de match
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
	if($done==0 || $done==4)
		$done++;
	$q = "UPDATE prono_matchs
	    SET score_A=$score_A, score_B=$score_B, done=$done, penalties=$penalties
	    WHERE id_match=$id_match";
	$result = mysql_query($q) or die(mysql_error());
	echo "Score du match enregistr� : ".$result."<br>";
	
	//Mise � jour des paris//
	/////////////////////////
	$q="SELECT * FROM prono_paris WHERE id_match=$id_match";
	$r=mysql_query($q) or die(mysql_error());
	if($done==1){   //////////////////////////////// Cas POULES
		while ($pari = mysql_fetch_array($r)){  // Pour chaque pari
			$id_pari= $pari['id_pari'];
			$pari_A = $pari['pari_A'];
			$pari_B = $pari['pari_B'];
			$win    = $pari['win'];
			if( ($score_A<$score_B && $win == 'B')
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
		
	}elseif($done==5){ ////////////////////////////// Cas FINALES
		$id_team_A = $match['id_team_A'];  //�quipes r�elles (�a n'a de sens qu'en finale)
		$id_team_B = $match['id_team_B'];
		$phase = $match['phase'];
		while($pari = mysql_fetch_array($r)){   // Pour chaque pari
			$id_pari= $pari['id_pari'];
			$pari_A = $pari['pari_A'];
			$pari_B = $pari['pari_B'];
			$win    = $pari['win'];
			$id_team_A_p = $pari['id_team_A'];
			$id_team_B_p = $pari['id_team_B'];
			$penalties_p = $pari['penalties'];

			//Gestion des points en phase finales
			if(  ($score_A<$score_B && $win == 'B' && $id_team_B == $id_team_B_p)
			   ||($score_B<$score_A && $win == 'A' && $id_team_A == $id_team_A_p)){ //Si j'ai le bon vainqueur
			   // J'avais pr�dit B gagnant et j'avais bien devin� B
			   // OU J'avais pr�dit A gagnant et j'avais bien devin� A
				$points=3;

				if($phase=='finale'){
	    			$points = 7; // Points de base finale
					if($id_team_A_p==$id_team_A && $id_team_B_p==$id_team_B){
						$points +=5; // Bonus bonnes �quipes
						
						if( ($score_A == $pari_A && $score_B == $pari_B && $penalties==0 && $penalties_p==0)
						  ||($penalties==1 && $penalties_p==1 && min($score_A,$score_B)==min($pari_A,$pari_B)))
							$points +=5; // Bonus bon score (cas penalties/pas p�nalties)
     				}
     				
				} elseif($phase=='8emes'){
					if ($id_team_A_p==$id_team_A && $id_team_B_p==$id_team_B //Il faut avoir les bonnes �quipes
					 && (($score_A == $pari_A && $score_B == $pari_B && $penalties==0 && $penalties_p==0)
					  || ($penalties==1 && $penalties_p==1 && min($score_A,$score_B)==min($pari_A,$pari_B))))
						$points +=2; // Juste un bonus bon score (cas penalties/pas p�nalties)
						
				} else{
					if($id_team_A_p==$id_team_A && $id_team_B_p==$id_team_B){
						$points +=2; // Bonus bonnes �quipes
						
						if( ($score_A == $pari_A && $score_B == $pari_B && $penalties==0 && $penalties_p==0)
						  ||($penalties==1 && $penalties_p==1 && min($score_A,$score_B)==min($pari_A,$pari_B)))
							$points +=2; // Bonus bon score (cas tirs au but/pas tirs au but)
					}
				}
				
			}elseif( $penalties==1 && $penalties_p==1
				&& $id_team_A_p==$id_team_A
				&& $id_team_B_p==$id_team_B){//Seule exception � la r�gle du bon vainqueur: tirs au but
				
				$points = 2;
				
				if (min($score_A,$score_B)==min($pari_A,$pari_B))
					$points +=2;
					
			}else
				$points = 0;

			$q = "UPDATE prono_paris
			SET points=$points
			WHERE id_pari=$id_pari";
			$result=mysql_query($q) or die(mysql_error());
		}
	}else
        echo "GROS BUG DANS UPDATE_MATCH";
        
  echo "Paris pris en compte : ".$result."<br>";
}

// Annuler un match
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

// Mettre � jour les score
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

// Annuler toutes les finales
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

// Eliminer une �quipe
if(isset($_POST['eliminer_team'])){
  $id_team = $_POST['eliminer_team'];
  $q = "UPDATE prono_teams SET playing=0 WHERE id_team=$id_team";
  $r = mysql_query($q) or die(mysql_error());
  echo "�quipe �limin�e :".$r;
}

// Se loguer "as"
if(isset($_POST['log_as'])){
  $id_user = $_POST['log_as'];
  $_SESSION['current_ID'] = $id_user;
  echo "logged as : ".$id_user;
}

$_POST=array();

?>












<div class='col-md-10 col-md-offset-1'>
<table class="table">
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
	echo ">Pr�-poules (pronos)<br>";
	echo "<input type='radio' name='change_phase' value='1' ";
	  if($phase['value_int']==1) echo "CHECKED";
	echo ">Poules (pronos phases finales)<br>";
	echo "<input type='radio' name='change_phase' value='2' ";
	  if($phase['value_int']==2) echo "CHECKED";
	echo ">Phases finales (plus de pronos)<br>";
	?>
	</td>
	</form>
  </tr>
  
  
  <tr><th colspan="2" align='left'>Updater les vainqueurs en phases finales</th></tr>

  <tr><td> </td><th bgcolor='CCCCFF' align='left'>Vainqueurs poules : ATTENTION OPERATION IRREVERSIBLE</th></tr>
<?php
	print_form_matchs_finale(get_matchs_8emes(), 'CCCCFF', 'update_wins_poule');
?>
  <tr><td> </td><th bgcolor='99FF99' align='left'>Vainqueurs huiti�mes : ATTENTION OPERATION IRREVERSIBLE</th></tr>
<?php
	$n_test = mysql_num_rows(mysql_query("SELECT id_match FROM prono_matchs WHERE phase='8emes' AND done=3"));
	if($n_test==0)
		print_form_matchs_finale(get_matchs_4rts(), '99FF99', 'update_wins_8emes');	
?>
  <tr><td> </td><th bgcolor='FFCC99' align='left'>Vainqueurs quarts : ATTENTION OPERATION IRREVERSIBLE</th></tr>
<?php
	$n_test = mysql_num_rows(mysql_query("SELECT id_match FROM prono_matchs WHERE phase='4rts' AND done=3"));
	if($n_test==0)     //// Plus de match de 4rts � renseigner
		print_form_matchs_finale(get_matchs_demis(), 'FFCC99', 'update_wins_4rts');
?>
  <tr><td> </td><th bgcolor='FFCCFF' align='left'>Vainqueurs demis : ATTENTION OPERATION IRREVERSIBLE</th></tr>
<?php
	$n_test = mysql_num_rows(mysql_query("SELECT id_match FROM prono_matchs WHERE phase='demis' AND done=3"));
	if($n_test==0)
		print_form_matchs_finale(get_match_finale(), 'FFCCFF', 'update_wins_demis');
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
		while ($match = mysql_fetch_array($r)){     // Pour tous les matchs � done = 1 ou 5
			$nameA = get_name($match['id_team_A']);
			$nameB = get_name($match['id_team_B']);
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
</div>

<?php print_html_footer(False); ?>
