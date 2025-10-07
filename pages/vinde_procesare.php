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
$error_message = "";
$success_message = "";

// Procesare adăugare telefon vândut
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['adauga_telefon'])) {
    $nume_telefon = mysqli_real_escape_string($db, $_POST['nume_telefon']);
    $stare_telefon = mysqli_real_escape_string($db, $_POST['stare_telefon']);
    $sanatate_baterie = mysqli_real_escape_string($db, $_POST['sanatate_baterie']);
    $memorie = mysqli_real_escape_string($db, $_POST['memorie']);
    $culoare = mysqli_real_escape_string($db, $_POST['culoare']);
    $numar_telefon_vanzator = mysqli_real_escape_string($db, $_POST['numar_telefon_vanzator']);

    // Validări suplimentare dacă este necesar
    // ...

    // Inserează înregistrarea în tabelul TelefoaneVandute
    $insert_sql = "INSERT INTO TelefoaneVandute (NumeTelefonVandut, StareTelefon, SanatateBaterie, Memorie, Culoare, IDUser, NumarTelefon) 
                   VALUES ('$nume_telefon', '$stare_telefon', '$sanatate_baterie', $memorie, '$culoare', $user_id, '$numar_telefon_vanzator')";
    
    if (mysqli_query($db, $insert_sql)) {
        $success_message = "Telefonul a fost adăugat cu succes în baza de date.";
    } else {
        $error_message = "Eroare la adăugarea telefonului: " . mysqli_error($db);
    }
}

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaugă Telefon Vândut</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
</head>
<body>

<?php include 'meniu.php'; ?>

<div class="grid-container">
    <div class="grid-x grid-padding-x">
        <div class="medium-6 cell">
            <h3>Vinde Telefon</h3>
            <?php if (!empty($error_message)): ?>
                <div class="callout alert">
                    <?= $error_message ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <div class="callout success">
                    <?= $success_message ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <label>Nume Telefon:
                    <input type="text" name="nume_telefon" required>
                </label>
                <label>Stare Telefon:
                    <input type="text" name="stare_telefon" required>
                </label>
                <label>Sanatate Baterie:
                    <input type="text" name="sanatate_baterie" required>
                </label>
                <label>Memorie (GB):
                    <input type="number" name="memorie" required>
                </label>
                <label>Culoare:
                    <input type="text" name="culoare" required>
                </label>
                <label>Număr Telefon Vânzător:
                    <input type="text" name="numar_telefon_vanzator" required>
                </label>
                <input type="submit" class="button" name="adauga_telefon" value="Adaugă Telefon">
            </form>
        </div>
    </div>
</div>
<h3><a href>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/js/foundation.min.js"></script>
<script>
    $(document).foundation();
</script>
</body>
</html>
