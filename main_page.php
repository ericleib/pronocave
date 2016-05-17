<?php require_once("commons.php"); ?>
<?php
kick_out_intruders(False);

// Poster un message
if(isset($_POST['message'])){
	$id_user = $_SESSION['current_ID'];
	$q = mysql_fetch_array(mysql_query("SELECT login FROM prono_users WHERE id_user=$id_user"));
	$login = $q['login'];
	$text = addslashes($_POST['message']);
	$date = date("Y-m-d H:i:s", time());
	$q = "INSERT INTO prono_messages(login,text,date)
		  VALUES('$login','$text','$date')";
	mysql_query($q) or die(mysql_error());
	$id_msg = mysql_insert_id();
	
	if(isset($_FILES['photo'])){
		if ($_FILES['photo']['error'] == 0){
			$extension_upload = strtolower(  substr(  strrchr($_FILES['photo']['name'], '.')  ,1)  );
			$nom = "images/{$id_msg}.{$extension_upload}";
			$resultat = move_uploaded_file($_FILES['photo']['tmp_name'],$nom);
		}
	}
	header("Location:main_page.php#messages");
}

// Supprimer un message
if(isset($_POST['delete_msg'])){
	$id_message = $_POST['delete_msg'];
	$q= "DELETE FROM prono_messages WHERE id_message=$id_message";
	mysql_query($q) or die(mysql_error());
	header("Location:main_page.php#messages");
}

function get_photos(){
  $imgs = array();
  $iterator = new DirectoryIterator("images/");
  foreach ($iterator as $fileinfo) {
    if ($fileinfo->isFile()) {
	  $name = $fileinfo->getFilename();
      $imgs[intval($name)] = "images/".$name;
    }
  }
  return $imgs;
}

?>

<?php print_html_header("Pronocave 2016", True); ?>

<h1>Salut <?php echo get_user_name(); ?> !</h1>

<?php   // ADMIN
if($_SESSION['privilege']=='admin')
	echo "<a href='admin.php'>Accès aux outils d'administration</a><br><br>";
?>

		
<div class=" col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 text-left margin_down">
<table  class="table table-striped table-hover margin_down">
<thead>
<tr><th width= '10'>clas.</th><th>Nom</th><th>Score</th><th>Bonus</th></tr>
</thead>
<tbody>
<?php   // Tableau users
  $q = "SELECT value_int FROM prono_vars WHERE name='phase'";
  $r = mysql_query($q) or die(mysql_error());
  $phase = mysql_fetch_array($r);
  
  $q = "SELECT id_user,login, score, bonus FROM prono_users ORDER BY score DESC, bonus DESC, login ASC";
  $r = mysql_query($q) or die(mysql_error());
  $cpt_users = 1;
  $score_even = 0;
  $score_prev = 1000;
  while($user = mysql_fetch_array($r)){
    $id_user=$user['id_user'];
	$score = $user['score'];
	$login = $user['login'];
	$bonus = $user['bonus'];
	if($score==$score_prev)
		$score_even++;
	else
		$score_even=0;
	$cpt_af = $cpt_users - $score_even;
	$cpt_users++;
	$score_prev=$score;
	$class="";
	if($login==get_user_name()){
		$class = " class=\"info\"";
	}
	echo "<tr".$class."><td width= '10'>".$cpt_af."</td><td>";
	if($phase['value_int']==0)
		echo $login;
	elseif($phase['value_int']==1)
		echo "<a href='paris.php?log_as=".$id_user."'>".$login."</a>";
	elseif($phase['value_int']==2)
        echo "<a href='paris_finales.php?log_as=".$id_user."'>".$login."</a>";
	echo "</td><td>".$score."</td><td>".$bonus."</td></tr>";
  }
?>
</tbody>
</table>

<form role="form" action="" method='post' enctype="multipart/form-data">
  <div class="form-group">
    <label for="message">Ecrire un message:</label>
    <textarea wrap='soft' name="message" id="message" cols="50" rows="5" class="form-control"></textarea>
  </div>
  <div class="form-group">
    <label for="photo">Ajouter une photo:</label>
    <input type="file" name="photo" id="photo" accept="image/*" />
  </div>
  <button type="submit" class="btn btn-default pull-right">Envoyer</button>
  <script type="text/javascript">
	CKEDITOR.replace( 'message' );
  </script>
</form>

<div class="clearfix"></div>

<?php

$messagesParPage = 30; //Nous allons afficher 30 messages par page.
$retour_total = mysql_query('SELECT COUNT(*) AS total FROM prono_messages'); //Nous récupérons le contenu de la requête dans $retour_total
$donnees_total = mysql_fetch_assoc($retour_total); //On range retour sous la forme d'un tableau.
$nombreDePages = ceil($donnees_total['total']/$messagesParPage);
$pageActuelle = isset($_GET['page'])? min(intval($_GET['page']), $nombreDePages) : 1; 
$premiereEntree=($pageActuelle-1)*$messagesParPage; // On calcul la première entrée à lire
 
$q = "SELECT * FROM prono_messages ORDER BY date DESC LIMIT ".$premiereEntree.', '.$messagesParPage.'';
$r = mysql_query($q) or die(mysql_error());
$id_user = $_SESSION['current_ID'];
$r_user = mysql_fetch_array(mysql_query("SELECT login,privilege FROM prono_users WHERE id_user=$id_user"));
$current_user = $r_user['login'];
$current_priv = $r_user['privilege'];
$imgs = get_photos();
while($message= mysql_fetch_array($r)){
  $text = $message['text'];
  $text = preg_replace('/  /', '&nbsp;&nbsp;', $text);
  $text = nl2br(stripslashes($text));
  $user = $message['login'];
  $date = strftime("%A %e %b à %k:%M", strtotime($message['date']));
  //$date = date("l d/m at h:i", $message['date']);
  $id_message = $message['id_message'];
  echo "<div class='message panel panel-default'>";
  if(strlen(trim($text))>0){
	echo "<div class='panel-body'>".$text."</div>";
  }
  if(array_key_exists($id_message, $imgs)){
	echo "<img src='".$imgs[$id_message]."' class='img-responsive center-block'>";
  }
  echo "<div class='panel-footer text-right'> <span><b>".$user."</b> (".$date.")";
  if ($current_priv =='admin' || $current_user==$user){
	echo "<form action='' method='post'><input type='hidden' name='delete_msg' value='$id_message'>";
	echo "<button type=\"submit\" class=\"btn btn-default btn-sm\">Supprimer</button></form>";
  }
  echo "</span></div></div>";
}

echo "<ul class=\"pagination\">"; //Pour l'affichage, on centre la liste des pages
for($i=1; $i<=$nombreDePages; $i++){ //On fait notre boucle
  echo "<li";
  if($i==$pageActuelle){
	  echo " class=\"active\"><a href=\"#messages\">".$i."</a></li>";
  }else{
	  echo "><a href=\"main_page.php?page=".$i."\">".$i."</a></li>";
  }
}
echo '</ul>';
?>
</div>

<div class='col-sm-6 col-sm-offset-3 col-md-3 col-md-offset-0 margin_down'>
	<div class="panel panel-default ">
		<div class="panel-heading">Stats</div>
		<div class="panel-body text-left">
<?php   // Cadran Statistiques
  $r = mysql_query("SELECT id_user FROM prono_users") or die(mysql_error());
  $n_users = mysql_num_rows($r);
  $r = mysql_query("SELECT id_pari FROM prono_paris") or die(mysql_error());
  $n_paris = mysql_num_rows($r);
  $r = mysql_query("SELECT id_match FROM prono_matchs WHERE done=1 OR done=5") or die(mysql_error());
  $n_matchs_done = mysql_num_rows($r);
  $r = mysql_query("SELECT id_match FROM prono_matchs WHERE done=0 OR done=3 OR done=4") or die(mysql_error());
  $n_matchs_undone = mysql_num_rows($r);
  echo "<b>".$n_users."</b> bouteilles dans la cave<br>";
  echo "<b>".$n_paris."</b> pronos enregistrés<br>";
  echo "<b>".$n_matchs_done."</b> matchs terminés<br>";
  echo "<b>".$n_matchs_undone."</b> matchs à venir<br>";
?>
	</div>
	</div>
</div>
		
<div class="col-sm-6 col-sm-offset-3 col-md-3 col-md-offset-0">
<a class="twitter-timeline" href="https://twitter.com/EURO2016" data-widget-id="731963009447415808">Tweets de @EURO2016</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
</div>

<?php print_html_footer(True); ?>
