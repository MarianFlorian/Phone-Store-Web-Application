<?php
// db_connection.php

$db = mysqli_connect("127.0.0.1", "root", "", "WEB");

if (!$db) {
    die("Eroare la conectarea la bază de date: " . mysqli_connect_error());
}
?>
