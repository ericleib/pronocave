<?php require_once("db_connect.php"); ?>
<?php session_start();

//phpinfo();

if(isset($_POST['login'])){
  $login=addslashes($_POST['login']);
  $pass =addslashes(sha1($_POST['pass']));

  $q = "SELECT * FROM prono_users WHERE login='$login' AND pass='$pass'";
  $result = mysql_query($q) or die(mysql_error());
  $row_verif = mysql_fetch_array($result);
  $user = mysql_num_rows($result);

  if($user){
  
	$_SESSION['privilege'] = $row_verif['privilege'];
	$_SESSION['login'] = $row_verif['login'];
	$_SESSION['current_ID'] = $row_verif['id_user'];
	header("Location:main_page.php");
  }
  else
	header("Location:index.php?out=login");
}
?>
<?php if(isset($_GET['out']) && ($_GET['out'] == "deco")) {
	$_SESSION = array();
	header("Location:index.php?out=adios"); }?>
<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
	<title> Pronocave 2012 - Identification </title>
<meta httpequiv="ContentType" content="text/html; charset=windows-1252" />
<link rel="shortcut icon" href="BallonFoot.gif" type="image/x-icon"/>
<link rel="icon" href="BallonFoot.gif" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" href="style_div.css">
</head>
<body>

	<div class="header"> <img src="banniere.jpg" width=410 height=120> </div>

	<div class="contenu">
<table border="0" cellspacing="0" align='center'>
<form action="" method="post">
  <tr>
	<th colspan="2"> <b>Identifiez-vous </b></th>
	<th rowspan="3" width="100">
	  <a href="new_user.php"><u>Ou créez<br>un nouveau<br>compte</u></a> </th>
  </tr>
  <tr>
    <td width="100" align='right'>Login</td>
    <td><input type="text" name="login"></td>
  </tr>
  <tr>
    <td width="100" align='right'>Password</td>
    <td><input type="password" name="pass"></td>
  </tr>
  <tr>
    <td width="100"></td>
    <td align='right'><input type="submit" value="Valider"></td>
  </tr>
</form>
</table>
<?php if(isset($_GET['out']) && ($_GET['out'] == "login")) { ?>
  <p align='center' style='color:red'><b>Login ou mot de passe incorrect</b></p>
<?php } ?>
<?php if(isset($_GET['out']) && ($_GET['out'] == "adios")) { ?>
  <p align='center'><b>A bient&ocirc;t !</b></p>
<?php } ?>
<?php if(isset($_GET['out']) && ($_GET['out'] == "intru")) { ?>
  <p align='center' style='color:red'><b>Identification requise </b></p>
<?php } ?>
<?php if(isset($_GET['out']) && ($_GET['out'] == "rights")) { ?>
  <p align='center' style='color:red'><b>Compte administrateur requis pour accéder à cette page</b></p>
<?php } ?>
	 </div>
</body>
</html>
