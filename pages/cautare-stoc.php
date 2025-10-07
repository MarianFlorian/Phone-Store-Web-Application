<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vizualizare dupa cautare</title>
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
</head>
<body>

<?php include 'meniu.php'; ?>

<h2>Vizualizare</h2>
</br>

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nume = isset($_POST['nume']) ? trim($_POST['nume']) : '';
    $baterie = isset($_POST['baterie']) ? trim($_POST['baterie']) : '';
    $culoare = isset($_POST['culoare']) ? trim($_POST['culoare']) : '';
    $conditie = isset($_POST['conditie']) ? trim($_POST['conditie']) : '';
    $pret = isset($_POST['pret']) ? $_POST['pret'] : '';

    $db = mysqli_connect("127.0.0.1", "root", "");
    if (!$db) {
        die("Conexiunea la baza de date a eșuat: " . mysqli_connect_error());
    }
    mysqli_select_db($db, "WEB");

    $sql = "SELECT * FROM Produse WHERE 1";

    if (!empty($nume)) {
        $sql .= " AND Nume LIKE '%" . mysqli_real_escape_string($db, $nume) . "%'";
    }

    if (!empty($baterie)) {
        $sql .= " AND Baterie LIKE '%" . mysqli_real_escape_string($db, $baterie) . "%'";
    }

    if (!empty($culoare)) {
        $sql .= " AND Baterie LIKE '%" . mysqli_real_escape_string($db, $culoare) . "%'";
    }

    if (!empty($conditie)) {
        $sql .= " AND Conditie LIKE '%" . mysqli_real_escape_string($db, $conditie) . "%'";
    }

    if (!empty($pret)) {
        $sql .= " AND Pret <= $pret";
    }

    $rezultat = mysqli_query($db, $sql);

    if (mysqli_num_rows($rezultat) > 0) {
        echo '<table>
                <thead>
                    <tr>
                        <th>Nume</th>
                        <th>Preț</th>
                        <th>Baterie</th>
                        <th>Condiție</th>
                    </tr>
                </thead>
                <tbody>';

        while($rand = mysqli_fetch_assoc($rezultat)) {
            echo "<tr>";
            echo "<td>" . $rand["Nume"] . "</td>";
            echo "<td>" . $rand["Pret"] . "</td>";
            echo "<td>" . $rand["Baterie"] . "</td>";
            echo "<td>" . $rand["Conditie"] . "</td>";
            echo "</tr>";
        }

        echo '</tbody>
              </table>';
    } else {
        echo "<p>Nu s-au găsit produse care să corespundă căutării.</p>";
    }

  
}
?>


</br>
<a href="tabel-telefoane-stoc.html" class="button">Operatii</a>
<script src="js/vendor/jquery.js"></script>
<script src="js/vendor/what-input.js"></script>
<script src="js/vendor/foundation.js"></script>
<script src="js/app.js"></script>





</html>
