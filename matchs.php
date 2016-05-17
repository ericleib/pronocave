<?php require_once("commons.php"); ?>
<?php kick_out_intruders(False); ?>

<?php print_html_header("Matchs", True); ?>

<h1> Matchs terminés</h1>
<div class="col-md-6 col-md-offset-3 margin_down">
<table id='past' class='table table-striped table-condensed'>
<tr><th> Matchs </th><th> Score </th><th> Cote </th>
	<th> Total<br>points </th><th>Mon<br>prono</th><th>Mes<br>points</th></tr>
<?php
$id_user = $_SESSION['current_ID'];
$q = "SELECT * FROM prono_matchs WHERE done=1 OR done=5 ORDER BY date DESC";
$r_matchs = mysql_query($q) or die(mysql_error());
while($match = mysql_fetch_array($r_matchs)){   // Pour chaque match terminé
    $id_match = $match['id_match'];
    $id_team_A= $match['id_team_A'];
    $id_team_B= $match['id_team_B'];
    $score_A  = $match['score_A'];
    $score_B  = $match['score_B'];
    $date     = $match['date'];
    $phase     = $match['phase'];
    $date     = strtotime($date);
    $date     = date('j/m',$date);
    $nameA = get_name($id_team_A);
    $nameB = get_name($id_team_B);
    $cote_A=0;
    $cote_B=0;
    $points=0;
    $q = "SELECT * FROM prono_paris WHERE id_match=$id_match";
    $r = mysql_query($q)or die(mysql_error());
    $points_user = 0;
    $penalties = 0; 	// Attention : différent de $match['penalties']
    while($pari = mysql_fetch_array($r)){   // Pour chaque pari sur ce match
		$points += $pari['points'];//rajouter stats !
		if($id_user==$pari['id_user']) $points_user = $pari['points'];
		if($match['done']==1 || ($id_team_A==$pari['id_team_A'] && $id_team_B==$pari['id_team_B'])){
			$win = $pari['win'];
			if ($win=='A')
				$cote_A++;
			elseif($win=='B')
			    $cote_B++;
			else{
				$cote_A++;
				$cote_B++;
			}
			if($id_user==$pari['id_user']){
				$pari_user_A = $pari['pari_A'];
				$pari_user_B = $pari['pari_B'];
				$penalties   = $pari['penalties'];
			}
		}
    }
    if($cote_A != 0 || $cote_B != 0){
		if($cote_A == 0)
			$cote_B='&#8734';
		elseif($cote_B == 0)
			$cote_A='&#8734';
		elseif($cote_A>=$cote_B){
			$cote_A = round($cote_A/$cote_B,2);
			$cote_B = 1;}
		else{
			$cote_B = round($cote_B/$cote_A,2);
			$cote_A = 1;
		}
    }
    echo "<tr match='$id_match'>";
    echo "<td id='teams'";
    	// Colorer les lignes du tableau
		if ($phase=='8emes') echo "style='color:#6600CC'";
	    if ($phase=='4rts') echo "style='color:#009900'";
	    if ($phase=='demis') echo "style='color:#CC6600'";
	    if ($phase=='finale') echo "style='color:#993399'";
	echo ">";
	
	// Mettre en gras l'équipe gagnante
	if($score_A>=$score_B) echo "<b>".$nameA."</b>";
	else echo $nameA;
	echo "  -  ";
	if($score_B>=$score_A) echo "<b>".$nameB."</b>";
	  else echo $nameB;

	echo "</td>";

	// Afficher le score
	if($match['penalties']==1){     // En cas de péno, le score est juste là pour stocker le vainqueur
		$score_A= min($score_A,$score_B);
		$score_B= $score_A;
	}
    echo "<td>".$score_A."  -  ".$score_B."</td>";
    echo "<td>".$cote_A."  :  ".$cote_B."</td>";
    echo "<td>".$points."</td>";
    

    // Afficher les paris
	if(isset($pari_user_A)){
		if($penalties==1){
			$pari_user_A= min($pari_user_A,$pari_user_B);
			$pari_user_B= $pari_user_A;
		}
		echo "<th style='color:red'>".$pari_user_A."  -  ".$pari_user_B."</th>";
		unset($pari_user_A);
		unset($pari_user_B);
    }else
        echo "<td style='color:red'><b>X</b></td>";

	echo "<th style='color:red'>";
	if($points_user>0) echo "+".$points_user;
		else echo "-";
	echo "</th></tr>";
}
?>

<?php 

$q = "SELECT * FROM prono_matchs,prono_paris 
WHERE prono_matchs.id_match=prono_paris.id_match AND prono_matchs.phase='finale'";
$r_paris = mysql_query($q) or die(mysql_error());

$teams = array();
while($pari = mysql_fetch_array($r_paris)){   // Pour chaque pari sur la finale

    if($pari['pari_A'] > $pari['pari_B']){
	$teams[$pari['id_team_A']] = 1 + (array_key_exists($pari['id_team_A'],$teams) ? $teams[$pari['id_team_A']] : 0);
    }else{
	$teams[$pari['id_team_B']] = 1 + (array_key_exists($pari['id_team_B'],$teams) ? $teams[$pari['id_team_B']] : 0);
    }

}

asort($teams);

$plot_team = '';
$plot_points = '';
foreach ($teams as $key => $val) {

	if($plot_team==''){
	    $plot_team = "'".utf8_encode(get_name($key))."'";
	}else{
	    $plot_team = "'".utf8_encode(get_name($key))."',".$plot_team;
	}

	if($plot_points==''){
	    $plot_points = $val;
	}else{
	    $plot_points = $val.",".$plot_points;
	}

}
?>
</table>
</div>
	
<div class="col-md-6 col-md-offset-3 margin_down">
<h1> Matchs à venir</h1>
<table id='tocome' class='table table-striped table-condensed'>
<tr><th> Date </th><th> Matchs</th><th> Cote </th><th>Mon<br>prono</th> </tr>
<?php
$id_user = $_SESSION['current_ID'];
$q = "SELECT * FROM prono_matchs WHERE done=0 OR done=4 ORDER BY date ASC";
$r_matchs = mysql_query($q) or die(mysql_error());
while($match = mysql_fetch_array($r_matchs)){
    $id_match = $match['id_match'];
    $id_team_A= $match['id_team_A'];
    $id_team_B= $match['id_team_B'];
    $done     = $match['done'];
    $date     = $match['date'];
    $phase    = $match['phase'];
    $date     = strtotime($date);
    $date     = date('j/m',$date);
    $nameA = get_name($id_team_A);
    $nameB = get_name($id_team_B);
    $cote_A=0;
    $cote_B=0;
    if ($done==0)   // Phase de poule
    	$q = "SELECT win,pari_A,pari_B,id_user,penalties FROM prono_paris WHERE id_match=$id_match";
    else            // Phases finales done =4 -> On ne sélectionne le paris que si ce sont les bonne équipes !
        $q = "SELECT win,pari_A,pari_B,id_user,penalties FROM prono_paris WHERE id_match=$id_match
		      AND id_team_A=$id_team_A AND id_team_B=$id_team_B";
	$r = mysql_query($q)or die(mysql_error());
	$penalties = 0;
    while($pari = mysql_fetch_array($r)){   // On parcourt tous les paris pour calculer la cote
		$win = $pari['win'];
		if ($win=='A')
			$cote_A++;
		elseif($win=='B')
			$cote_B++;
		else{
			$cote_A++;
			$cote_B++;}
		if($id_user==$pari['id_user']){     // On garde en mémoire le pari de l'utilisateur, si il existe !
			$pari_user_A = $pari['pari_A'];
			$pari_user_B = $pari['pari_B'];
			$penalties = $pari['penalties'];
		}
    }
    if($cote_A != 0 || $cote_B != 0){
		if($cote_A == 0)
			$cote_B='&#8734';
		elseif($cote_B == 0)
			$cote_A='&#8734';
		elseif($cote_A>=$cote_B){
			$cote_A = round($cote_A/$cote_B,2);
			$cote_B = 1;}
		else{
			$cote_B = round($cote_B/$cote_A,2);
			$cote_A = 1;
		}
    }

    echo "<tr><td>".$date."</td>";
    echo "<td ";
		if ($phase=='8emes') echo "style='color:#6600CC'";
	    if ($phase=='4rts') echo "style='color:#009900'";
	    if ($phase=='demis') echo "style='color:#CC6600'";
	    if ($phase=='finale') echo "style='color:#993399'";
	echo ">".$nameA."  -  ".$nameB."</td>";
	
    echo "<td>".$cote_A."  :  ".$cote_B."</td>";
	if(isset($pari_user_A)){
		if($penalties==1){
			$pari_user_A = min($pari_user_A,$pari_user_B);
			$pari_user_B = $pari_user_A;
		}
		echo "<th style='color:red'>".$pari_user_A."  -  ".$pari_user_B."</th></tr>";
		unset($pari_user_A);
		unset($pari_user_B);
	}else
		echo "<td style='color:red'><b>X</b></td></tr>";
}
?>
</table>
</div>

<div class="col-md-6 col-md-offset-3 text-left margin_down">
<font color=6600CC>Huitièmes de finale</font> - 
<font color=009900>Quarts de finale</font> -
 <font color=CC6600>Demi-finales</font> -
 <font color=993399>Finale</font><br><br>
<u><b>Cote</b></u>: La cote indique quelle équipe est donnée gagnante.<br>
"1:5" indique que l'équipe B est donnée 5 fois plus gagnante que l'équipe A.<br>
"1:1" indique que les pronostics sont équilibrés, ou que le match nul est beaucoup pronostiqué.<br><br>
</div>


<?php if( get_phase()==2 ) { ?>

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto">

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="http://code.highcharts.com/highcharts.js"></script>

		<!-- script src="http://code.highcharts.com/modules/data.js"></script -->
		<!-- script src="http://code.highcharts.com/modules/exporting.js"></script -->
		<!-- Additional files for the Highslide popup effect -->
		<!-- script type="text/javascript" src="http://www.highcharts.com/media/com_demo/highslide-full.min.js"></script>
		<script type="text/javascript" src="http://www.highcharts.com/media/com_demo/highslide.config.js" charset="utf-8"></script>
		<link rel="stylesheet" type="text/css" href="http://www.highcharts.com/media/com_demo/highslide.css" / -->

		<!--script type="text/javascript" src="test.js"></script--> 

<script type="text/javascript">
$(document).ready(function() {

	// Test de highcharts
	$(function () {

		$('#container').highcharts({

		    chart: { type: 'column' },

		    title: {
		        text: 'Qui va gagner la Coupe du Monde selon vous ??',
		        x: -20 //center
		    },
		    /*subtitle: {
		        text: 'Source: WorldClimate.com',
		        x: -20
		    },*/
		    xAxis: {
		        categories: [<?php echo $plot_team;?>],
			labels: {
				rotation: -45,
				align: 'right'
                	}
		    },
		    yAxis: {
		        title: {
		            text: 'Points'
		        },
		        plotLines: [{
		            value: 0,
		            width: 1,
		            color: '#808080'
		        }],
			tickInterval: 1
		    },
		    tooltip: {
		        valueSuffix: 'pts'
		    },
		    /*legend: {
		        layout: 'vertical',
		        align: 'right',
		        verticalAlign: 'middle',
		        borderWidth: 0
		    },*/
		    series: [{
		        name: 'Nombre de pronos',
		        data: [<?php echo $plot_points;?>]
		    }/*, {
		        name: 'Bonus',
		        data: [<?php echo $plot_bonus;?>]
		    }*/]
		});

	});

});
</script></div>

<?php } ?>

<?php print_html_footer(True); ?>
