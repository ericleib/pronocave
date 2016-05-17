<?php require_once("commons.php"); ?>
<?php kick_out_intruders(True); ?>
<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
       <title>Gestion table matchs</title>
       <meta httpequiv="ContentType" content="text/html; charset=windows-1252" />
<link rel="shortcut icon" href="BallonFoot.gif" type="image/x-icon"/>
<link rel="icon" href="BallonFoot.gif" type="image/x-icon"/>
</head>
<body>
<table width="450" border="0" align="left">
  <tr>
	<form action="" method="post">
	<input type="hidden" name="parse_matchs_txt" value="1">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>	</td>
	<td> Parser le fichier "matchs.txt" </td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<input type="hidden" name="fill_table_matchs" value="1">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>	</td>
	<td> Remplir la table prono_matchs </td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<input type="hidden" name="reset_table_matchs" value="1">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>	</td>
	<td> Reseter la table prono_matchs </td>
	</form>
  </tr>
  <tr><td colspan="2">
    <p align="center"><a href="admin.php"> Retour au panneau d'administration </a></p>
  </td></tr>
</table><br>

<?php
if(isset($_POST['reset_table_matchs'])){
  $q = "DELETE FROM prono_matchs";
  mysql_query($q) or die(mysql_error);
  echo "table resetée avec succès";
}

if(isset($_POST['parse_matchs_txt'])){
  $lines = file('matchs.txt') or die("problème ouverture fichier");
  echo "<table border='1' width='600'><tr><th> Equipe A </th><th>
  Equipe B</th><th colspan='2'>Date</th></tr>";
  foreach($lines as $line){
	$step= 0;
	$words = explode(" ",$line);
	$teamA = "";
	$teamB = "";
	$date  = "";
	$time  = "";
	foreach($words as $id_word => $word){
	  if($word != ""){
        if($step==3)
		  $teamB = $teamB.$word." ";
		  elseif($step==2){
		    if($word == "-")
			  $step = 3;
		      else
	  	        $teamA = $teamA.$word." ";
	  	  }
	  		elseif($step==1 && preg_match("#[0-9]#",$word) == 1){
	  	      $time = $word;
		      $step = 2;
	  	    }
	  	      elseif(preg_match("#[0-9]/[0-9]#",$word) == 1){
		        $date = $word;
		        $step = 1;
	  		  }
	  }
	}
	$teamA = trim($teamA);
	$teamB = trim($teamB);
    echo "<tr><td>".$teamA."</td><td>".$teamB."</td><td>".$date."</td><td>".$time."</td></tr>";
  }
  echo "</table>";
}

if(isset($_POST['fill_table_matchs'])){

  mysql_query("SELECT * FROM prono_teams")
    or die("veuillez créer et remplir la table prono_teams svp");
  mysql_query("SELECT * FROM prono_matchs")
    or die("veuillez créer la table prono_matchs svp");
    
  $lines = file('matchs.txt') or die("problème ouverture fichier");
  foreach($lines as $line){
	$step= 0;
	$words = explode(" ",$line);
	$teamA = "";
	$teamB = "";
	$date  = "";
	$time  = "";
	foreach($words as $id_word => $word){
	  if($word != ""){
        if($step==3)
		  $teamB = $teamB.$word." ";
		  elseif($step==2){
		    if($word == "-")
			  $step = 3;
		      else
	  	        $teamA = $teamA.$word." ";
	  	  }
	  		elseif($step==1 && preg_match("#[0-9]#",$word) == 1){
	  	      $time = $word;
		      $step = 2;
	  	    }
	  	      elseif(preg_match("#[0-9]/[0-9]#",$word) == 1){
		        $date = $word;
		        $step = 1;
	  		  }
	  }
	}
	$teamA = addslashes(trim($teamA));
	$teamB = addslashes(trim($teamB));
	$date = trim($date);
	$time = trim($time);
	$qA = "SELECT * FROM prono_teams WHERE name='$teamA'";
	$qB = "SELECT * FROM prono_teams WHERE name='$teamB'";
	$resultA = mysql_query($qA) or die("incapable de trouver ".$teamA);
	$resultB = mysql_query($qB) or die("incapable de trouver ".$teamB);
	$resultA = mysql_fetch_array($resultA);
	$resultB = mysql_fetch_array($resultB);
	$poule = $resultA['poule'];
	$id_teamA = $resultA['id_team'];
	$id_teamB = $resultB['id_team'];
	list($day,$month)= explode("/",$date);
	list($hour,$minutes)= explode(":",$time);
	$datetime = "2016-".$month."-".$day." ".$hour.":".$minutes.":00";
	$q = "INSERT INTO prono_matchs(id_team_A,id_team_B,poule,date)
		  VALUES('$id_teamA','$id_teamB','$poule','$datetime')";
	mysql_query($q) or die(mysql_error());
  }
  echo "table remplie avec succès";
}

?>

</body>
</html>
