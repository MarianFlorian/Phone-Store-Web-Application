<?php

if (isset($_GET['id'])) {
    
    $db = mysqli_connect("127.0.0.1", "root", "");
    mysqli_select_db($db, "WEB");

    
    $idProdus = $_GET['id'];

    
    $sql = "DELETE FROM Produse WHERE IDProdus = $idProdus";

  
    if (mysqli_query($db, $sql)) {
       
        mysqli_close($db);
        
        header("Location: sterge-stoc.php");
        exit;
    } else {
        echo "<p>Eroare la ștergerea produsului: " . mysqli_error($db) . "</p>";
    }
} else {
    
    echo "<p>Eroare: ID-ul produsului lipsește.</p>";
}
?>
