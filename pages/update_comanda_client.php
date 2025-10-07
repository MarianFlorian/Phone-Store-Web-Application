<?php
session_start();
require_once 'access_functions.php'; // Include fișierul cu funcțiile de acces

// Conectare la baza de date
$db = mysqli_connect("127.0.0.1", "root", "", "WEB");

// Verificare conexiune
if (!$db) {
    die("Conexiunea la baza de date a eșuat: " . mysqli_connect_error());
}

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    echo "Nu sunteți autentificat. Autentificați-vă pentru a edita comanda.";
    exit();
}

// Preluăm datele trimise prin POST
if (isset($_POST['id']) && isset($_POST['adresaLivrare'])) {
    $idComanda = mysqli_real_escape_string($db, $_POST['id']);
    $adresaLivrare = mysqli_real_escape_string($db, $_POST['adresaLivrare']);

    // Verificăm dacă comanda aparține utilizatorului autentificat
    $user_id = $_SESSION['user_id'];
    $sqlCheck = "SELECT IDComanda FROM Comenzi WHERE IDComanda = '$idComanda' AND IDUser = '$user_id'";
    $resultCheck = mysqli_query($db, $sqlCheck);

    if (mysqli_num_rows($resultCheck) > 0) {
        // Actualizăm adresa livrării pentru comanda specificată
        $sqlUpdate = "UPDATE Comenzi SET AdresaLivrare = '$adresaLivrare' WHERE IDComanda = '$idComanda'";
        if (mysqli_query($db, $sqlUpdate)) {
            echo "Adresa livrării pentru comanda cu ID-ul $idComanda a fost actualizată cu succes.";
        } else {
            echo "Eroare la actualizarea adresei livrării: " . mysqli_error($db);
        }
    } else {
        echo "Nu aveți permisiunea să editați această comandă.";
    }
} else {
    echo "Date insuficiente pentru actualizare.";
}

mysqli_close($db);
?>
