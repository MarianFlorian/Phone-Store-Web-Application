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
        .container {
            margin-top: 30px;
            text-align: center;
        }
        .button-container {
            margin-top: 20px;
        }
        .button-container a {
            margin: 5px;
        }
    </style>
</head>
<body>
<?php include 'meniu.php'; ?>
<div class="container">
    <h2>Operații Stoc</h2>
    <div class="button-container">
        <a href="vizualizare-stoc.php" class="button">Vizualizare</a>
        <a href="adaugare-stoc.php" class="button">Adaugă</a>
        <a href="sterge-stoc.php" class="button">Șterge/Editeaza</a>
        <a href="cautare-stoc-formular.php" class="button">Caută</a>
    </div>
</div>

<script src="js/vendor/jquery.js"></script>
<script src="js/vendor/what-input.js"></script>
<script src="js/vendor/foundation.js"></script>
<script src="js/app.js"></script>

</body>
</html>
