<?php require_once("db_connect.php"); ?>
<?php session_start();

if ($_SESSION['login']){}
else {
header("Location:index.php?out=intru");
}?>

<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
       <title>R�gles</title>
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
<p>La r�gle du Pronocave est simple: Chaque joueur met en jeu une bouteille de son meilleur vin et �met des pronostics sur l'issue des matchs de la coupe d'Europe. Le plus fin pronostiqueur gagnera la cave* � la fin du tournoi.
Le jeu se d�compose en plusieurs p�riodes:</p>
- <b>Phase de pronostics:</b> Tout le monde peut effectuer ses pronos et les modifier sur ce site jusqu'au d�but des matchs (08/06).<br>
- <b>Phase de groupes:</b> Tout le monde peut effectuer ses pronos UNIQUEMENT SUR LES PHASES FINALES et les modifier sur ce site jusqu'au d�but de celles-ci (21/06).<br>
- <b>Phases finales:</b> Plus aucun prono n'est modifiable jusqu'�la fin de la coupe.<br>
<p>Les points sont comptabilis�s de la mani�re suivante:</p>
- <b>Phase de groupes:</b> 2 points si l'issue du match est correctement pronostiqu�e (Bon vainqueur ou �galit�) et 3 points si le score est exact.<br>
- <b>Phases finales :</b> 3 points si on a le bon vainqueur**, +2 points si on a le bon vaincu (sauf en quarts) et +2 points si on a le bon score. Pour la finale, m�me r�gle avec 7/+5/+5 points.<br>
<br>
<p>Pour toute r�clamation, si futile soit-elle, envoyez-moi un <a href="mailto:eric.leibenguth@gmail.com">mail</a> !</p><br><br>
<small>
*: Cave nette<br>
(Cave nette = Cave brute - ponction effectu�e par les organisateurs)<br>
**: Seule exception �cette r�gle, si on pronostique correctement un match nul avec tirs au but mais qu'on se trompe de vainqueur. Dans ce cas on gagne 2 points, et +2 points si le score est exact.
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
