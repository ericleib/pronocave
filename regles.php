<?php require_once("commons.php"); ?>
<?php kick_out_intruders(False); ?>

<?php print_html_header("R�gles", True); ?>

<h1> R�gles du jeu </h1>
<div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 text-left margin_down">
<p>La r�gle du Pronocave est simple: Chaque joueur met en jeu une bouteille de son meilleur vin et �met des pronostics sur l'issue des matchs de la Coupe du Monde. Le plus fin pronostiqueur gagnera la cave* � la fin du tournoi.
Le jeu se d�compose en plusieurs p�riodes:</p>
- <b>Phase de pronostics:</b> Tout le monde peut effectuer ses pronos et les modifier sur ce site jusqu'au d�but des matchs (12/06).<br>
- <b>Phase de groupes:</b> Tout le monde peut effectuer ses pronos UNIQUEMENT SUR LA PHASE FINALE et les modifier sur ce site jusqu'au d�but de celles-ci (28/06).<br>
- <b>Phase finale:</b> Plus aucun prono n'est modifiable jusqu'�la fin de la coupe.<br>
<p>Les points sont comptabilis�s de la mani�re suivante:</p>
- <b>Phase de groupes:</b> 2 points si l'issue du match est correctement pronostiqu�e (Bon vainqueur ou �galit�) et 3 points si le score est exact.<br>
- <b>Phase finale :</b> 3 points si on a le bon vainqueur**, +2 points si on a le bon vaincu (sauf en huiti�mes) et +2 points si on a le bon score. Pour la finale, m�me r�gle avec 7/+5/+5 points.<br>
<br>
<p>Pour toute r�clamation, si futile soit-elle, envoyez-moi un <a href="mailto:eric.leibenguth@gmail.com">mail</a> !</p><br><br>
<small class="margin_down">
*: Cave nette<br>
(Cave nette = Cave brute - ponction effectu�e par les organisateurs)<br>
**: Seule exception �cette r�gle, si on pronostique correctement un match nul avec tirs au but mais qu'on se trompe de vainqueur. Dans ce cas on gagne 2 points, et +2 points si le score est exact.
</small>
</div>
<?php print_html_footer(True); ?>