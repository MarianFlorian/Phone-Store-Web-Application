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

$user_id = $_SESSION['user_id'];
$product_id = $_GET['id'];

// Verificare dacă s-a trimis formularul pentru editare
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_telefon'])) {
    $nume = $_POST['nume'];
    $conditie = $_POST['conditie'];
    $baterie = $_POST['baterie'];
    $culoare = $_POST['culoare'];
    $pret = $_POST['pret'];

    // Actualizare telefon în baza de date
    $update_sql = "UPDATE Produse 
                   SET Nume = '$nume', Conditie = '$conditie', 
                       Baterie = '$baterie', Culoare = '$culoare', Pret = $pret
                   WHERE IDProdus = $product_id";
    
    if (mysqli_query($db, $update_sql)) {
        header("Location: sterge-stoc.php");
        exit();
    } else {
        echo "Eroare la actualizarea produsului: " . mysqli_error($db);
    }
}

// Preluare informații despre telefonul de editat
$sql = "SELECT * FROM Produse WHERE IDProdus = $product_id";
$result = mysqli_query($db, $sql);

if (mysqli_num_rows($result) > 0) {
    $product = mysqli_fetch_assoc($result);
} else {
    echo "Produsul nu există.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editează Telefon</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
</head>
<body>

<?php include 'meniu.php'; ?>

<div class="grid-container">
    <div class="grid-x grid-padding-x">
        <div class="medium-6 cell">
            <h3>Editează Telefon</h3>
            <form method="post">
                <label>Nume
                    <input type="text" name="nume" value="<?= htmlspecialchars($product['Nume']) ?>" required>
                </label>
                <label>Condiție
                    <input type="text" name="conditie" value="<?= htmlspecialchars($product['Conditie']) ?>" required>
                </label>
                <label>Baterie
                    <input type="text" name="baterie" value="<?= htmlspecialchars($product['Baterie']) ?>" required>
                </label>
                <label>Culoare
                    <input type="text" name="culoare" value="<?= htmlspecialchars($product['Culoare']) ?>" required>
                </label>
                <label>Preț
                    <input type="number" name="pret" value="<?= htmlspecialchars($product['Pret']) ?>" required>
                </label>
                <input type="hidden" name="product_id" value="<?= $product['IDProdus'] ?>">
                <input type="submit" class="button" name="update_telefon" value="Actualizează">
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/js/foundation.min.js"></script>
<script>
    $(document).foundation();
</script>
</body>
</html>
