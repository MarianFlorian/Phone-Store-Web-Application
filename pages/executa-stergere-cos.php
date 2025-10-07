<?php
session_start();
$db = mysqli_connect("127.0.0.1", "root", "", "WEB");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_comanda'])) {
    $id_comanda = $_POST['id_comanda'];

    // Șterge produsele din coș
    $delete_cart_sql = "DELETE FROM Cos WHERE IDUser = '$user_id'";
    mysqli_query($db, $delete_cart_sql);

    // Șterge produsele din tabelul Produse
    $sql = "SELECT IDProdus FROM DetaliiComenzi WHERE IDComanda = '$id_comanda'";
    $result = mysqli_query($db, $sql);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $product_id = $row['IDProdus'];
        $delete_produs_sql = "DELETE FROM Produse WHERE IDProdus = '$product_id'";
        mysqli_query($db, $delete_produs_sql);
    }

    mysqli_close($db);

    header("Location: cos.php?success=1");
    exit();
} else {
    header("Location: cos.php?error=1");
    exit();
}
