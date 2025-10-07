<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vizualizare dupa cautare</title>
  <!-- Include Foundation CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
  <!-- Stiluri personalizate -->
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
    /* campuri */
    form {
      max-width: 400px;
      margin: 0 auto;
    }
    label {
      display: block;
      margin-bottom: 5px;
    }
    input[type="text"],
    input[type="number"] {
      width: 100%;
      margin-bottom: 10px;
      padding: 8px;
      box-sizing: border-box;
    }
    input[type="submit"] {
      background-color: #4CAF50;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      width: 100%;
    }
    input[type="submit"]:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>

<?php include 'meniu.php'; 
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
<br>

<div class="form-container">
    
    <form method="POST" action="cautare-stoc.php">
        <h2>Căutare produse</h2>
        <label for="nume">Nume produs:</label>
        <input type="text" id="nume" name="nume">
        
        <label for="baterie">Baterie:</label>
        <input type="text" id="baterie" name="baterie">
        
        <label for="culoare">Culoare:</label>
        <input type="text" id="culoare" name="culoare">
        
        <label for="conditie">Condiție:</label>
        <input type="text" id="conditie" name="conditie">
        
        <label for="pret">Preț:</label>
        <input type="number" id="pret" name="pret" min="0">
        
        <input type="submit" class="button" value="Caută produse">
    </form>
</div>

<div class="form-container text-center"> <!-- Container div pentru a centra butonul -->
    <a href="tabel-telefoane-stoc.php" class="button">Operatii</a>
</div>

<script src="js/vendor/jquery.js"></script>
<script src="js/vendor/what-input.js"></script>
<script src="js/vendor/foundation.js"></script>
<script src="js/app.js"></script>

</body>
</html>