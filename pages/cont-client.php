<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
require_once 'access_functions.php'; // Include fișierul cu funcțiile de acces

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$page = basename(__FILE__); // Obține numele fișierului curent

// Verifică drepturile de acces
if (!check_user_access($user_id, $page)) {
  header("Location: access_denied.php");
  exit();
}
$user_id = $_SESSION['user_id'];
$pages = get_menu_pages($user_id); // Obține paginile disponibile pentru utilizator

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cont Client</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
</head>
<body>



  <?php include 'meniu.php'; ?>
<h2>Bine ai venit!</h2>


</body>
</html>
