<?php require_once("commons.php"); ?>
<?php kick_out_intruders(False); ?>

<?php print_html_header("Règles", True); ?>

<h1> Règles du jeu </h1>
<div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 text-left margin_down">
<p>La règle du Pronocave est simple: Chaque joueur met en jeu une bouteille de son meilleur vin et émet des pronostics sur l'issue des matchs de la Coupe du Monde. Le plus fin pronostiqueur gagnera la cave* à la fin du tournoi.
Le jeu se décompose en plusieurs périodes:</p>
- <b>Phase de pronostics:</b> Tout le monde peut effectuer ses pronos et les modifier sur ce site jusqu'au début des matchs (12/06).<br>
- <b>Phase de groupes:</b> Tout le monde peut effectuer ses pronos UNIQUEMENT SUR LA PHASE FINALE et les modifier sur ce site jusqu'au début de celles-ci (28/06).<br>
- <b>Phase finale:</b> Plus aucun prono n'est modifiable jusqu'à la fin de la coupe.<br>
<p>Les points sont comptabilisés de la manière suivante:</p>
- <b>Phase de groupes:</b> 2 points si l'issue du match est correctement pronostiquée (Bon vainqueur ou égalité) et 3 points si le score est exact.<br>
- <b>Phase finale :</b> 3 points si on a le bon vainqueur**, +2 points si on a le bon vaincu (sauf en huitièmes) et +2 points si on a le bon score. Pour la finale, même règle avec 7/+5/+5 points.<br>
<br>
<p>Pour toute réclamation, si futile soit-elle, envoyez-moi un <a href="mailto:eric.leibenguth@gmail.com">mail</a> !</p><br><br>
<small class="margin_down">
*: Cave nette<br>
(Cave nette = Cave brute - ponction effectuée par les organisateurs)<br>
**: Seule exception à cette règle, si on pronostique correctement un match nul avec tirs au but mais qu'on se trompe de vainqueur. Dans ce cas on gagne 2 points, et +2 points si le score est exact.
</small>
</div>
<?php print_html_footer(True); ?>