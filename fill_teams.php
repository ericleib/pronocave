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
       <title>Gestion table teams</title>
       <meta httpequiv="ContentType" content="text/html; charset=windows-1252" />
<link rel="shortcut icon" href="BallonFoot.gif" type="image/x-icon"/>
<link rel="icon" href="BallonFoot.gif" type="image/x-icon"/>
</head>
<body>
<table width="450" border="0" align="left">
  <tr>
	<form action="" method="post">
	<input type="hidden" name="parse_teams_txt" value="1">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>	</td>
	<td> Parser le fichier "teams.txt" </td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<input type="hidden" name="fill_table_teams" value="1">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>	</td>
	<td> Remplir la table prono_teams </td>
	</form>
  </tr>
  <tr>
	<form action="" method="post">
	<input type="hidden" name="reset_table_teams" value="1">
	<td width="30"><p align="right"><input type="submit" value="OK"></p>	</td>
	<td> Reseter la table prono_teams </td>
	</form>
  </tr>
  <tr><td colspan="2">
    <p align="center"><a href="admin.php"> Retour au panneau d'administration </a></p>
  </td></tr>
</table><br>

<?php
if(isset($_POST['reset_table_teams'])){
  $q = "DELETE FROM prono_teams";
  mysql_query($q) or die(mysql_error);
  echo "table resetée avec succès";
}

if(isset($_POST['parse_teams_txt'])){
  $lines = file('teams.txt') or die("problème ouverture fichier");
  $poule = 'A';
  $nbteam = 0;
  $id_team = 0;
  echo "<table>";
  foreach($lines as $line){
	if($nbteam == 0)
	  echo "<tr><th> Poule ".$poule."</th></tr>";
	$words = explode(" ",$line);
	$teams[$id_team]= "";
	foreach($words as $id_word => $word){
	  if($word != "" && $word != "-" && preg_match("#[0-9]#",$word) == 0)
		$teams[$id_team]=$teams[$id_team].$word." " ;
	}
	$teams[$id_team]=trim($teams[$id_team]);
    echo "<tr><td>".$id_team."</td><td>".$teams[$id_team]."</td></tr>";
    $nbteam++ ;
    $id_team++ ;
    if($nbteam == 4){
	  $nbteam = 0;
	  $poule++;
    }
  }
  echo "</table>";
}

if(isset($_POST['fill_table_teams'])){
  
  mysql_query("SELECT * FROM prono_teams") or die("veuillez créer la table prono_teams svp");
  
  $lines = file('teams.txt') or die("problème ouverture fichier");
  $poule = 'A';
  $nbteam = 0;
  $id_team = 0;
  foreach($lines as $line){
	$line = addslashes($line);
	$words = explode(" ",$line);
	$teams[$id_team]= "";
	foreach($words as $id_word => $word){
	  if($word != "" && $word != "-" && preg_match("#[0-9]#",$word) == 0)
		$teams[$id_team]=$teams[$id_team].$word." " ;
	}
	$teams[$id_team]=trim($teams[$id_team]);
	$q = "INSERT INTO prono_teams(name,poule) VALUES('$teams[$id_team]','$poule')";
	$result= mysql_query($q) or die(mysql_error());
    $nbteam++ ;
    $id_team++ ;
    if($nbteam == 4){
	  $nbteam = 0;
	  $poule++;
    }
  }
  echo "table remplie avec succès";
}

?>

</body>
</html>
