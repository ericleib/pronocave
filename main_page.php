<?php require_once("db_connect.php"); ?>
<?php session_start();

if ($_SESSION['current_ID']){}
else {
header("Location:index.php?out=intru");
}

if(isset($_POST['message'])){
$id_user = $_SESSION['current_ID'];
$q = mysql_fetch_array(mysql_query("SELECT login FROM prono_users WHERE id_user=$id_user"));
$login = $q['login'];
$text = addslashes($_POST['message']);
$date = date("Y-m-d H:i:s", time());
$q = "INSERT INTO prono_messages(login,text,date)
	  VALUES('$login','$text','$date')";
mysql_query($q) or die(mysql_error());
header("Location:main_page.php#messages");
}

if(isset($_POST['delete_msg'])){
$id_message = $_POST['delete_msg'];
$q= "DELETE FROM prono_messages WHERE id_message=$id_message";
mysql_query($q) or die(mysql_error());
header("Location:main_page.php#messages");
}
?>
<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>

	<title> Pronocave 2010 </title>
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

<h1>Salut
<?php
$id_user = $_SESSION['current_ID'];
$q = mysql_fetch_array(mysql_query("SELECT login FROM prono_users WHERE id_user=$id_user"));
echo $q['login']; ?> !</h1>
<?php
if($_SESSION['privilege']=='admin')
  echo "<a href='admin.php'>Accès aux outils d'administration</a><br><br>";
?>

<table width='300' border='1' cellspacing='0' align='center'>
<tr><th colspan='4'>Classement</th></tr>
<tr><th width= '10'>clas.</th><th>Nom</th><th>Score</th><th>Bonus</th></tr>
<?php
  $q = "SELECT value_int FROM prono_vars WHERE name='phase'";
  $r = mysql_query($q) or die(mysql_error());
  $phase = mysql_fetch_array($r);
  
  $q = "SELECT id_user,login, score, bonus FROM prono_users ORDER BY score DESC, login ASC";
  $r = mysql_query($q) or die(mysql_error());
  $cpt_users = 1;
  $score_even = 0;
  $score_prev = 1000;
  while($user = mysql_fetch_array($r)){
    $id_user=$user['id_user'];
	$score = $user['score'];
	$login = $user['login'];
	$bonus = $user['bonus'];
	if($score==$score_prev)
	  $score_even++;
	  else
	    $score_even=0;
	$cpt_af = $cpt_users - $score_even;
	$cpt_users++;
	$score_prev=$score;
	echo "<tr><td width= '10'>".$cpt_af."</td><td>";
	if($phase['value_int']==0)
	  echo $login;
	  elseif($phase['value_int']==1)
		echo "<a href='paris.php?log_as=".$id_user."' style='color:black'>".$login."</a>";
		elseif($phase['value_int']==2)
          echo "<a href='paris_finales.php?log_as=".$id_user."' style='color:black'>".$login."</a>";
	echo "</td><td>".$score."</td><td>".$bonus."</td></tr>";
  }
?>
</table>
<br><br>

<table border='0' cellspacing='0' align='center'>
<tr><th align='left' >Ecrire un message :</th></tr>
<tr><td><form action="" method='post'>
<textarea wrap='soft' name="message" cols="50" rows="5"></textarea>
<p align='right'><input type="submit" value="Envoyer"></p>
</form>
</td></tr></table>
<a name="messages"></a>
<?php
$q = "SELECT * FROM prono_messages ORDER BY date DESC LIMIT 0,50 ";
$r = mysql_query($q) or die(mysql_error());
$id_user = $_SESSION['current_ID'];
$r_user = mysql_fetch_array(mysql_query("SELECT login,privilege FROM prono_users WHERE id_user=$id_user"));
$current_user = $r_user['login'];
$current_priv = $r_user['privilege'];
while($message= mysql_fetch_array($r)){
  $text = $message['text'];
  $text = preg_replace('/  /', '&nbsp;&nbsp;', $text);
  $text = nl2br(stripslashes($text));
  $user = $message['login'];
  $date = $message['date'];
  $id_message = $message['id_message'];
  echo "<div class='message'><p align=left>".$text."</p>";
  echo "<h1 align='right'> Message posté par ".$user." le ".$date."</h1>";
  if ($current_priv =='admin' || $current_user==$user){
	echo "<form action='' method='post'><input type='hidden' name='delete_msg' value='$id_message'>";
	echo "<p align='right'><input type='submit' value='Supprimer'></p></form>";
  }
  echo "</div>";
}
?>
	 </p></div>
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
