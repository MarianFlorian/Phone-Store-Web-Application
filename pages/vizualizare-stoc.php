<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vizualizare stoc</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
  <style>
    .phone-table {
      margin-top: 30px;
      text-align: center; 
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
    .phone-table img {
      max-width: 100px;
      max-height: 100px;
    }
  </style>
</head>
<body>

<?php include 'meniu.php'; ?>

<h2>Vizualizare</h2>

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

$sql = "SELECT * FROM Produse";
$result = mysqli_query($db, $sql);

if (mysqli_num_rows($result) > 0) {
    echo '<div class="phone-table">';
    echo '<table>';
    echo '<tr>
          
          <th>Nume</th>
          <th>Preț</th>
          <th>Baterie</th>
          <th>Culoare</th>
          <th>Condiție</th>
          </tr>';

    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        //echo '<td><img src="' . $row['Imagine'] . '" alt="' . $row['Nume'] . '"></td>';
        echo '<td>' . $row['Nume'] . '</td>';
        echo '<td>' . $row['Pret'] . '</td>';
        echo '<td>' . $row['Baterie'] . '</td>';
        echo '<td>' . $row['Culoare'] . '</td>';
        echo '<td>' . $row['Conditie'] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';
} else {
    echo "Nu există produse de afișat.";
}

mysqli_close($db);
?>

</br>
<a href="tabel-telefoane-stoc.php" class="button">Operatii</a>

<script src="js/vendor/jquery.js"></script>
<script src="js/vendor/what-input.js"></script>
<script src="js/vendor/foundation.js"></script>
<script src="js/app.js"></script>

</body>
</html>
