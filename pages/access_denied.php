<?php
// access_denied.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obținem rolul utilizatorului
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';

// Stabilim pagina de redirecționare în funcție de rolul utilizatorului
$redirect_url = 'index.php';
if ($user_type == '1') {
    $redirect_url = 'admin-dashboard.php';
} elseif ($user_type == '2') {
    $redirect_url = 'cont-client.php';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acces Interzis</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
</head>
<body>

  <!-- Include meniul din meniu.php -->
  <?php include 'meniu.php'; ?>

  <div class="grid-container">
      <div class="callout alert">
          <h2>Acces Interzis</h2>
          <p>Nu ai permisiunea de a accesa această pagină.</p>
          <p><a href="<?= $redirect_url ?>" class="button">Înapoi la Pagina Principală</a></p>
      </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/js/foundation.min.js"></script>
  <script>
      $(document).foundation();
  </script>
</body>
</html>
