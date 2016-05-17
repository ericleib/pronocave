<?php require_once("commons.php"); ?>
<?php print_html_header("Nouvel Utilisateur", False); ?>

<div class="col-md-6 col-md-offset-3 text-left">
	<h1 align="center"> Avertissements </h1>
<p>Bonjour, ami pronostiqueur !</p>
<p>Voici quelques petites choses � savoir sur ce site avant de cr�er un compte:</p>
<ul>
	<li>Ne te cr�e qu'<b>un seul</b> compte ! Si tu apparais deux fois dans le classement tu seras �limin�.</li>
	<li>Si tu te cr�es un compte, c'est que tu as une bouteille � mettre en jeu !</li>
	<li>Choisis un <b>nom d'utilisateur</b> qui t'identifie de mani�re claire (exemple: pr�nom, ou pr�nom+nom). Les pseudos obscures seront impitoyablement et arbitrairement renomm�s.</li>
	<li>Evite de divulguer l'adresse du site, dont la s�curit� n'est sans doute pas optimale... </li>
</ul>
</div>

<div class="col-md-6 col-md-offset-3 text-left margin_down">
<?php
if(!isset($_POST['login'])){?>
<h2 class="text-center">Choisis tes identifiants</h2>
<form role="form" action="" method='post'>
  <div class="form-group">
    <label for="login">Nom d'utilisateur:</label>
    <input type="text" name="login" id="login" class="form-control"></td>
  </div>
  <div class="form-group">
    <label for="pwd">Mot de passe:</label>
    <input type="password" name="pass" class="form-control" id="pwd">
  </div>
  <div class="form-group">
    <label for="email">Email:</label>
    <input type="email" name="mail" class="form-control" id="email">
  </div>
  <button type="submit" class="btn btn-default pull-right margin_down">Valider</button>
</form>
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
		echo "<p align='center' style='color:red'><b>Nom d'utilisateur d�j� utilis� !</b><br><a href='new_user.php'>Revenir</a></p>";
    else{
        $q = mysql_query("INSERT INTO prono_users (login,pass,privilege,score,mail)
		     VALUES('$login','$pass','none',0,'$mail');")
		    or die(mysql_error());
        echo "<p align='center'><b>Tu es bien enregistr� ! Tu peux te connecter d�s maintenant !</b><br>";
        echo '<a href="index.php">Clique ici pour revenir � l\'accueil</a></p>';
    }
}
?>

</div>

<?php print_html_footer(False); ?>
