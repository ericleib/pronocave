<?php require_once("db_connect.php"); ?>
<?php session_start();

if ($_SESSION['login']){}
else {
header("Location:index.php?out=intru");
}?>

<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
       <title>r�gles</title>
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
			<li><a href="regles.php">R�gles</a></li>
			<li><a href="paris.php">Pronostics</a></li>
			<li><a href="matchs.php">Matchs</a></li>
			<li><a href='index.php?out=deco'>D�connexion</a></li>
	</ul>
	</div>
	<div class="contenu">

<h1> R�gles du jeu </h1>
<div class="texte">
<p>La r�gle du Pronocave est simple: Chaque joueur met en jeu une bouteille de son meilleur vin et �met des pronostics sur l'issue des matchs de la coupe du monde. Le plus fin parieur gagnera la cave* � la fin du tournoi.
Le jeu se d�compose en plusieurs p�riodes:</p>
- <b>P�riode pr�-poules:</b> Tout le monde peut effectuer ses paris et les modifier sur le site jusqu'au d�but des matchs (10/06).<br>
- <b>Poules:</b> Tout le monde peut effectuer ses paris UNIQUEMENT SUR LES PHASES FINALES et les modifier sur le site jusqu'au d�but de celles-ci (26/06).<br>
- <b>Phases finales:</b> Plus aucun pari n'est modifiable jusqu'� la fin de la coupe.<br>
<p>Les points sont comptabilis�s de la mani�re suivante:</p>
- <b>Matchs de poules:</b> 2 points si l'issue du match est correctement pronostiqu�e (Bon vainqueur ou �galit�), 3 points si le score est exact.<br>
- <b>Matchs de phases finales :</b> 3 points si le bon vainqueur est pronostiqu�, plus 2 points si le vaincu est bon et plus 2 points si, en plus, le score est bon. Pour la finale, m�me r�gle avec 7/+5/+5 points.<br>
- En cas de <b>tirs au but</b> en <b>phases finales</b> : 0 points si les tirs au but n'�taient pas pronostiqu�s, 3 points si le vainqueur pronostiqu� �tait bien parmi les deux �quipes, plus 2 points si les 2 �quipes �taient bien pronostiqu�es OU si le vainqueur �tait bien pronostiqu�, et 7 points si tout �tait bon, score compris.<br>
<p>Pour toute r�clamation, si futile soit-elle, envoyez-moi un <a href="mailto:eric.leibenguth@gmail.com">mail</a> !</p><br><br>
<small>
*: Cave net<br>
(Cave net = Cave brut - ponction effectu�e par les organisateurs)
</small>
</div>
	</div>
    <div class="footer">
    	<ul id="boutons">
			<li><a href="main_page.php">Accueil</a></li>
			<li><a href="regles.php">R�gles</a></li>
			<li><a href="paris.php">Pronostics</a></li>
			<li><a href="matchs.php">Matchs</a></li>
			<li><a href='index.php?out=deco'>D�connexion</a></li>
	</ul>
	</div>
</body>
</html>
