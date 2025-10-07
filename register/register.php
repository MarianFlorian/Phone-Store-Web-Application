<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inregistrare</title>
    <link rel="stylesheet" href="https://dhbhdrzi4tiry.cloudfront.net/cdn/sites/foundation.min.css">
  </head>
  <body>

 <!-- Start Top Bar -->
 <div class="top-bar">
    <div class="top-bar-left">
      <ul class="menu">
        <li>Amanet Telefoane</a></li>
        <li><a href="/QuizUI/despre.html">Despre</a></li>

      </ul>
    </div>
    <div class="top-bar-right">
      <ul class="menu">
        <li><a href="/QuizUI/login.php">Log In</a></li>
  
      </ul>
    </div>
  </div>
  <!-- End Top Bar -->


<?php
// register.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = mysqli_connect("127.0.0.1", "root", "", "WEB");

    // Preluarea datelor din formular
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $parola = password_hash($_POST['parola'], PASSWORD_DEFAULT);
    $nume = mysqli_real_escape_string($db, $_POST['nume']);
    $prenume = mysqli_real_escape_string($db, $_POST['prenume']);
    $type = 2; // Tipul implicit este 2 (user normal)

    // Verificare dacă email-ul există deja în baza de date
    $check_email_query = "SELECT * FROM Users WHERE Email='$email' LIMIT 1";
    $result = mysqli_query($db, $check_email_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        if ($user['Email'] === $email) {
            echo "Email-ul există deja!";
            echo 'Apasa <a href="register.html">aici</a> ca sa incerci din nou';
        }
    } else {
        // Inserarea utilizatorului în baza de date
        $query = "INSERT INTO Users (Email, Parola, Nume, Prenume, type) 
                  VALUES ('$email', '$parola', '$nume', '$prenume', '$type')";

        if (mysqli_query($db, $query)) {
            echo "Înregistrare reușită!";
            echo 'Te rog să te loghezi <a href="/QuizUI/login.php">aici</a>';

        } else {
            echo "Eroare: " . mysqli_error($db);
        }
    }

    mysqli_close($db);
}
?>
