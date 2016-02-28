<?php require_once("db_connect.php"); ?>
<?php session_start();

if ($_SESSION['login']){}
else {
header("Location:index.php?out=intru");
}?>

<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
       <title>règles</title>
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

<h1> Règles du jeu </h1>
<div class="texte">
<p>La règle du Pronocave est simple: Chaque joueur met en jeu une bouteille de son meilleur vin et émet des pronostics sur l'issue des matchs de la coupe du monde. Le plus fin parieur gagnera la cave* à la fin du tournoi.
Le jeu se décompose en plusieurs périodes:</p>
- <b>Période pré-poules:</b> Tout le monde peut effectuer ses paris et les modifier sur le site jusqu'au début des matchs (10/06).<br>
- <b>Poules:</b> Tout le monde peut effectuer ses paris UNIQUEMENT SUR LES PHASES FINALES et les modifier sur le site jusqu'au début de celles-ci (26/06).<br>
- <b>Phases finales:</b> Plus aucun pari n'est modifiable jusqu'à la fin de la coupe.<br>
<p>Les points sont comptabilisés de la manière suivante:</p>
- <b>Matchs de poules:</b> 2 points si l'issue du match est correctement pronostiquée (Bon vainqueur ou égalité), 3 points si le score est exact.<br>
- <b>Matchs de phases finales :</b> 3 points si le bon vainqueur est pronostiqué, plus 2 points si le vaincu est bon et plus 2 points si, en plus, le score est bon. Pour la finale, même règle avec 7/+5/+5 points.<br>
- En cas de <b>tirs au but</b> en <b>phases finales</b> : 0 points si les tirs au but n'étaient pas pronostiqués, 3 points si le vainqueur pronostiqué était bien parmi les deux équipes, plus 2 points si les 2 équipes étaient bien pronostiquées OU si le vainqueur était bien pronostiqué, et 7 points si tout était bon, score compris.<br>
<p>Pour toute réclamation, si futile soit-elle, envoyez-moi un <a href="mailto:eric.leibenguth@gmail.com">mail</a> !</p><br><br>
<small>
*: Cave net<br>
(Cave net = Cave brut - ponction effectuée par les organisateurs)
</small>
</div>
	</div>
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
