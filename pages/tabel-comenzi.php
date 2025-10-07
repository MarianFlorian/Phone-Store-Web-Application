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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
  <style>
    .phone-table {
      margin-top: 30px;
    }
    .phone-table table {
      width: 100%;
      border-collapse: collapse;
    }
    .phone-table th, .phone-table td {
      padding: 8px;
      border: 1px solid #ddd;
    }
    .phone-table th {
      background-color: #f2f2f2;
    }
    .form-container {
      margin-top: 20px;
    }
  </style>

<?php include 'meniu.php'; ?>

</head>
<body>


<body>  
  <center><h2>Operatii</h2></center>
  
  </br><center><a href="vizualizare-comenzi.php" class="button">Vizualizare/Editare/Stergere</a></center>
  </br><center><a href="adaugare-comenzi.php" class="button">Adauga</a></center>

  </br><center><a href="cautare-comenzi.html" class="button">Cauta</a></center>
  
      <script src="js/vendor/jquery.js"></script>
      <script src="js/vendor/what-input.js"></script>
      <script src="js/vendor/foundation.js"></script>
      <script src="js/app.js"></script>
  
  </body>
</body>
</html>
