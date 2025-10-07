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

$error_message = "";
$success_message = "";

// Procesarea formularului
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nume = mysqli_real_escape_string($db, $_POST['nume']);
    $pret = mysqli_real_escape_string($db, $_POST['pret']);
    $baterie = mysqli_real_escape_string($db, $_POST['baterie']);
    $culoare = mysqli_real_escape_string($db, $_POST['culoare']);
    $conditie = mysqli_real_escape_string($db, $_POST['conditie']);

    // Procesarea imaginii
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["imagine"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verifică dacă fișierul este o imagine reală
    $check = getimagesize($_FILES["imagine"]["tmp_name"]);
    if ($check === false) {
        $error_message .= "Fișierul nu este o imagine. ";
        $uploadOk = 0;
    }

    // Permite doar anumite formate de fișiere
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $error_message .= "Sunt permise doar fișiere JPG, JPEG, PNG și GIF. ";
        $uploadOk = 0;
    }

    // Verifică dacă încărcarea fișierului a avut succes
    if ($uploadOk == 0) {
        $error_message .= "Fișierul nu a fost încărcat.";
    } else {
        if (move_uploaded_file($_FILES["imagine"]["tmp_name"], $target_file)) {
            // Inserarea produsului în baza de date
            $sql = "INSERT INTO Produse (Nume, Pret, Baterie, Culoare, Conditie, Imagine) 
                    VALUES ('$nume', '$pret', '$baterie', '$culoare', '$conditie', '$target_file')";

            if (mysqli_query($db, $sql)) {
                $success_message = "Produsul a fost adăugat cu succes!";
            } else {
                $error_message .= "Eroare la adăugarea produsului: " . mysqli_error($db);
            }
        } else {
            $error_message .= "A existat o eroare la încărcarea fișierului.";
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adăugare stoc</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
  <style>
    .form-container {
      max-width: 600px;
      margin: 30px auto;
      padding: 20px;
      background-color: #f9f9f9;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .form-container h2 {
      margin-top: 0;
      margin-bottom: 20px;
      font-size: 24px;
    }
    .form-container .callout {
      margin-bottom: 20px;
    }
    .form-container label {
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
    }
    .form-container input[type="text"],
    .form-container input[type="number"],
    .form-container input[type="file"] {
      width: calc(100% - 20px);
      padding: 8px;
      margin-bottom: 10px;
      border: 1px solid #ddd;
      border-radius: 3px;
    }
    .form-container input[type="submit"] {
      background-color: #007BFF;
      color: white;
      border: none;
      padding: 10px 20px;
      cursor: pointer;
      border-radius: 3px;
    }
    .form-container input[type="submit"]:hover {
      background-color: #0056b3;
    }
    .form-container .button {
      margin-top: 10px;
    }
  </style>
</head>
<body>

<?php include 'meniu.php'; ?>

<div class="form-container">
  <h2>Adăugare produs nou</h2>
  <?php if ($error_message): ?>
    <div class="callout alert">
      <?php echo $error_message; ?>
    </div>
  <?php endif; ?>
  <?php if ($success_message): ?>
    <div class="callout success">
      <?php echo $success_message; ?>
    </div>
  <?php endif; ?>
  <form method="POST" action="adaugare-stoc.php" enctype="multipart/form-data">
      <label for="nume">Nume produs:</label>
      <input type="text" id="nume" name="nume" required>
      
      <label for="pret">Preț produs:</label>
      <input type="number" id="pret" name="pret" min="0" required>
      
      <label for="baterie">Baterie:</label>
      <input type="text" id="baterie" name="baterie">

      <label for="culoare">Culoare:</label>
      <input type="text" id="culoare" name="culoare">

      <label for="conditie">Condiție:</label>
      <input type="text" id="conditie" name="conditie">
      
      <label for="imagine">Imagine:</label>
      <input type="file" id="imagine" name="imagine">

      <input type="submit" class="button" value="Adăugare produs">
  </form>
</div>

<div class="form-container text-center">
  <a href="tabel-telefoane-stoc.php" class="button">Operatii</a>
</div>

<script src="js/vendor/jquery.js"></script>
<script src="js/vendor/what-input.js"></script>
<script src="js/vendor/foundation.js"></script>
<script src="js/app.js"></script>

</body>
</html>
