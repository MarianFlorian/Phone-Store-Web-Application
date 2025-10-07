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

    // Șterge detaliile comenzii
    $sql = "DELETE FROM DetaliiComenzi WHERE IDComanda = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Șterge comanda
        $sql = "DELETE FROM Comenzi WHERE IDComanda = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'error';
    }

    $stmt->close();
}

mysqli_close($db);
?>
