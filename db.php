<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gbs_db";

/*
$servername = "mysql-kacepdom.alwaysdata.net";
$username = "kacepdom";
$password = "Jch00751";
$dbname = "kacepdom_api_data";
*/

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
