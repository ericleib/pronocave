<?php require_once("commons.php"); ?>
<?php

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
	
<?php print_html_header("Pronocave 2016 - Identification", False); ?>

<h2 class="text-center">Vos papiers, s'il-vous-plait.</h2>

<div class=" col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 text-left margin_down">
<form role="form" action="" method='post'>
  <div class="form-group">
    <label for="login">Nom d'utilisateur:</label>
    <input type="text" name="login" id="login" class="form-control"></td>
  </div>
  <div class="form-group">
    <label for="pwd">Mot de passe:</label>
    <input type="password" name="pass" class="form-control" id="pwd">
  </div>
  <button type="submit" class="btn btn-default pull-right">Valider</button>
</form>
</div>

<div class="clearfix"></div>

<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 margin_down">
  <a href="new_user.php">Ou créez un nouveau compte</a> </th>
</div>

</div>
<div class="row">
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
<?php print_html_footer(False); ?>
