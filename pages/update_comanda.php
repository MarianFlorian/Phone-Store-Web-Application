<?php
session_start();
require_once 'access_functions.php'; // Include fișierul cu funcțiile de acces

// Conectează-te la baza de date
$db = mysqli_connect("127.0.0.1", "root", "", "WEB");

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $cantitate = $_POST['cantitate'];
    $prettotal = $_POST['prettotal'];
    $datacomanda = $_POST['datacomanda'];

    $sql = "UPDATE DetaliiComenzi SET Cantitate = ?, PretTotal = ? WHERE IDComanda = ? AND IDProdus = (SELECT IDProdus FROM DetaliiComenzi WHERE IDComanda = ? LIMIT 1)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("iisi", $cantitate, $prettotal, $id, $id);

    if ($stmt->execute()) {
        $sql = "UPDATE Comenzi SET DataComanda = ? WHERE IDComanda = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("si", $datacomanda, $id);

        if ($stmt->execute()) {
            header("Location: vizualizare-comenzi.php");
        } else {
            echo "Eroare la actualizarea datei comenzii: " . $stmt->error;
        }
    } else {
        echo "Eroare la actualizarea detaliilor comenzii: " . $stmt->error;
    }

    $stmt->close();
}

mysqli_close($db);
?>
