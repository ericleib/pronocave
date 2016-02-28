<?php
$sql_serveur = "localhost";
$sql_base = "loiseauonline";
$sql_login = "root";
$sql_password = "";

@mysql_connect($sql_serveur,$sql_login,$sql_password) or die("Echec connection");
@mysql_select_db($sql_base);
?>
