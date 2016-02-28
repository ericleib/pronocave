<?php require_once("db_connect.php"); ?>
<?php session_start();?>
<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
	<title> Nouvel utilisateur </title>
	<meta httpequiv="ContentType" content="text/html; charset=windows-1252" />
<!--link rel="shortcut icon" href="BallonFoot.gif" type="image/x-icon"/-->
<!--link rel="icon" href="BallonFoot.gif" type="image/x-icon"/-->
	<link rel="stylesheet" type="text/css" href="style_div.css">
</head>
<body>
	<div class="header"> <img src="banniere.jpg" width=600 height=120> </div>
	<div class="contenu" align="right">
	<div class="texte">
	<h1 align="center"> Avertissements </h1>
<p>Bonjour, pronostiqueur !</p>
<p>Quelques petites choses à savoir sur ce site avant de créer un compte:</p>
<ul>
	<li>Ne te crée qu'<b>un et un seul</b> compte ! Si tu apparais deux fois dans le classement tu seras éliminé.</li>
	<li>Choisis un <b>login</b> qui t'identifie de manière claire et univoque (exemple: prénom, ou prénom+nom).</li>
	<li>Certaines requêtes peuvent prendre du temps, pas la peine de cliquer 20 fois sur "OK".</li>
	<li>Evite autant que possible d'utiliser les boutons "<b>Actualiser</b>", ou "<b>Précédent</b>".
	Et surtout, si apparaît un avertissement du type "Confirmation de renvoi de formulaire", clique "<b>Annuler</b>".</li>
	<li>Evite de trop divulguer l'adresse du site, dont la sécurité n'est sans doute pas parfaite... </li>
	<li>Si tu vas plus loin que cette page, c'est que tu as une bouteille à mettre en jeu !</li>
</ul>
	</div>
<?php
if(!isset($_POST['login'])){?>
<table width="300" border="0" align="center" style="border:1px solid black">
<th align="center" colspan="2">Choisis tes identifiants :</th>
<form action="" method="post">
<tr>
  <td width="100" align="right">Login :</td>
  <td align="left"><input type="text" name="login"></td>
</tr>
<tr>
  <td width="100" align="right">Password :</td>
  <td align="left"><input type="password" name="pass"></td>
</tr>
<tr>
  <td width="100" align="right">Mail :</td>
  <td align="left"><input type="text" name="mail" width="600"></td>
</tr>
<tr>
  <td width="100"></td>
  <td><input type="submit" value="Valider"></td>
</tr>
</form>
</table><br>
<?php
}
else{
  $login = addslashes($_POST['login']);
  $mail = addslashes($_POST['mail']);
  $pass =  $_POST['pass'];
  if ($login == '' || strlen($pass) < 6)
	die("<p align='center' style='color:red'><b>Nom d'utilisateur incorrect ou mot de passe trop court.</b>
	  <br><a href='new_user.php'>Revenir</a></p>");
  $pass = addslashes(sha1($_POST['pass']));
  unset($_POST['login'],$_POST['pass'],$_POST['mail']);
  $q = mysql_query("SELECT * FROM prono_users WHERE login='$login'")
       or die(mysql_error());
  $n = mysql_num_rows($q);
  	if($n > 1)
    	echo "<p align='center' style='color:red'>Gros bug DB users...<br><a href='new_user.php'>Revenir</a></p>";
	else if($n == 1)
		echo "<p align='center' style='color:red'><b>Nom d'utilisateur déjà utilisé !</b><br><a href='new_user.php'>Revenir</a></p>";
    else{
        $q = mysql_query("INSERT INTO prono_users (login,pass,privilege,score,mail)
		     VALUES('$login','$pass','none',0,'$mail');")
		    or die(mysql_error());
        echo "<p align='center'><b>Tu es bien enregistré ! Tu peux te connecter dès maintenant !</b><br>";
        echo '<a href="index.php">Clique ici pour revenir à l\'accueil</a></p>';
    }
}
?>

</div>
</body>
</html>
