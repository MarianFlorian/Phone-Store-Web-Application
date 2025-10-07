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

// Conectează-te la baza de date
$db = mysqli_connect("127.0.0.1", "root", "", "WEB");

// Obține toate produsele
$sql = "SELECT * FROM Produse";
$result = mysqli_query($db, $sql);


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
      <!-- Tabel cu produse -->
      <table>
          <thead>
              <tr>
                  <th>ID Produs</th>
                  <th>Nume</th>
                  <th>Condiție</th>
                  <th>Baterie</th>
                  <th>Culoare</th>
                  <th>Preț</th>
                  <th>Acțiuni</th>
              </tr>
          </thead>
          <tbody>
              <?php while ($row = mysqli_fetch_assoc($result)): ?>
                  <tr>
                      <td><?= htmlspecialchars($row['IDProdus']) ?></td>
                      <td><?= htmlspecialchars($row['Nume']) ?></td>
                      <td><?= htmlspecialchars($row['Conditie']) ?></td>
                      <td><?= htmlspecialchars($row['Baterie']) ?></td>
                      <td><?= htmlspecialchars($row['Culoare']) ?></td>
                      <td><?= htmlspecialchars($row['Pret']) ?></td>
                      <td>
                          <a href="editare-stoc.php?id=<?= htmlspecialchars($row['IDProdus']) ?>" class="button">Editează</a>
                          <a href="executa-stergere-telefon.php?id=<?= htmlspecialchars($row['IDProdus']) ?>" class="button alert" onclick="return confirm('Ești sigur că vrei să ștergi acest produs?');">Șterge</a>
                      </td>
                  </tr>
              <?php endwhile; ?>
          </tbody>
      </table>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/js/foundation.min.js"></script>
  <script>
      $(document).foundation();
  </script>
</body>
</html>
