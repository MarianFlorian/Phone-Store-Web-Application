<?php
session_start();

// Verificăm dacă formularul a fost trimis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['plaseaza_comanda'])) {
    // Conectarea la baza de date
    $db = mysqli_connect("127.0.0.1", "root", "");
    mysqli_select_db($db, "WEB");

    // Preluarea datelor din formular
    $nume = mysqli_real_escape_string($db, $_POST['nume']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $telefon = mysqli_real_escape_string($db, $_POST['telefon']);

    // Creăm o nouă comandă în tabelul Comenzi
    $sql = "INSERT INTO Comenzi (Nume, Email, Telefon) VALUES ('$nume', '$email', '$telefon')";
    if (mysqli_query($db, $sql)) {
        // Obținem ID-ul comenzii recent adăugate
        $id_comanda = mysqli_insert_id($db);

        // Adăugăm fiecare produs din coș în tabelul DetaliiComenzi
        foreach ($_SESSION['cart'] as $id_produs => $cantitate) {
            $sql_detalii = "INSERT INTO DetaliiComenzi (IDComanda, IDProdus, Cantitate) VALUES ($id_comanda, $id_produs, $cantitate)";
            mysqli_query($db, $sql_detalii);
        }

        // Ștergem coșul după plasarea comenzii
        unset($_SESSION['cart']);

        // Redirecționăm către o pagină de confirmare sau de mulțumire
        header("Location: confirmare_comanda.php");
        exit();
    } else {
        echo "A apărut o eroare la plasarea comenzii: " . mysqli_error($db);
    }

    mysqli_close($db);
} else {
    // Dacă utilizatorul a încercat să acceseze direct această pagină, îl redirecționăm
    header("Location: cos.php");
    exit();
}
?>
