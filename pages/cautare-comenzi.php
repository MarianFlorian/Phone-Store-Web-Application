<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cautare comenzi</title>
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


$idComanda = $nume = $pret = $data = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idComanda = isset($_POST['idComanda']) ? trim($_POST['idComanda']) : '';
    
    $pret = isset($_POST['pret']) ? $_POST['pret'] : '';
    $data = isset($_POST['data']) ? $_POST['data'] : '';

    $sql = "SELECT * FROM Comenzi WHERE 1";

    if (!empty($idComanda)) {
        $sql .= " AND IDComanda = '$idComanda'";
    }

 

    if (!empty($pret)) {
        $sql .= " AND PretTotal <= $pret";
    }

    if (!empty($data)) {
        $sql .= " AND DATE(DataComanda) = '$data'";
    }

    $result = mysqli_query($db, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo '<h2>Rezultate căutare:</h2>';
        echo '<table border="1">';
        echo '<tr>
                <th>ID Comandă</th>
                
                <th>Preț Total</th>
                <th>Data Comandă</th>
            </tr>';

        while($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . $row['IDComanda'] . '</td>';
          
            echo '<td>' . $row['PretTotal'] . '</td>';
            echo '<td>' . $row['DataComanda'] . '</td>';
            echo '</tr>';
        }

        echo '</table>';
    } else {
        echo '<p>Nu s-au găsit comenzi care să corespundă căutării.</p>';
    }

    mysqli_close($db);
}
?>
</br>
<a href="tabel-comenzi.php" class="button">Operatii</a>
<script src="js/vendor/jquery.js"></script>
    <script src="js/vendor/what-input.js"></script>
    <script src="js/vendor/foundation.js"></script>
    <script src="js/app.js"></script>
  </body>
</html>