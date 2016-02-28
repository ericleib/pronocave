<?php
$sql_serveur = "localhost";
$sql_base = "loiseauonline";
$sql_login = "loiseauonline";
$sql_password = "alaide";

@mysql_connect($sql_serveur,$sql_login,$sql_password) or die("Echec connection");
@mysql_select_db($sql_base);
?>