<?php
// admin-dashboard.php

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
</head>
<body>

  <!-- Include meniul din meniu.php -->
  <?php include 'meniu.php'; ?>

  <div class="grid-container">
      <h2>Admin Dashboard</h2>
      <div class="text-center">
    <a href="tabel-telefoane-stoc.php" class="button">Tabel Telefoane Stoc</a>
    <a href="vinde_admin.php" class="button">Telefoane in verificare</a>
    <a href="tabel-comenzi.php" class="button">Tabel Comenzi</a>
  </div>
  </div>

</body>
</html>
