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

// Funcție pentru a afișa mesajele de succes sau de eroare
function displayAlert($message, $type = 'success')
{
    return '<div class="callout ' . $type . '">' . $message . '</div>';
}

// Verificare dacă s-a trimis formularul pentru editare
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_telefon'])) {
    $telefon_id = $_POST['telefon_id'];
    $stare_telefon = $_POST['stare_telefon'];
    $sanatate_baterie = $_POST['sanatate_baterie'];
    $memorie = $_POST['memorie'];
    $culoare = $_POST['culoare'];
    $numar_telefon = $_POST['numar_telefon'];
    
    // Actualizare telefon în baza de date
    $update_sql = "UPDATE TelefoaneVandute 
                   SET StareTelefon = '$stare_telefon', SanatateBaterie = '$sanatate_baterie', 
                       Memorie = $memorie, Culoare = '$culoare', NumarTelefon = '$numar_telefon'
                   WHERE IDTelefon = $telefon_id AND IDUser = $user_id";
    
    if (mysqli_query($db, $update_sql)) {
        echo displayAlert("Telefonul a fost actualizat cu succes.");
    } else {
        echo displayAlert("Eroare la actualizarea telefonului: " . mysqli_error($db), 'alert');
    }
}

// Preluare informații despre telefonul de editat
if (isset($_POST['telefon_id'])) {
    $telefon_id = $_POST['telefon_id'];
    
    // Interogare pentru a obține detaliile telefonului
    $sql = "SELECT * FROM TelefoaneVandute WHERE IDTelefon = $telefon_id AND IDUser = $user_id";
    $result = mysqli_query($db, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $telefon = mysqli_fetch_assoc($result);
    } else {
        echo displayAlert("Telefonul nu există sau nu îți aparține.", 'alert');
        exit();
    }
} else {
    header("Location: vinde.php");
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
                <label>Nume Telefon
                    <input type="text" name="nume_telefon" value="<?= htmlspecialchars($telefon['NumeTelefonVandut']) ?>" required>
                </label>
                <label>Stare Telefon
                    <input type="text" name="stare_telefon" value="<?= htmlspecialchars($telefon['StareTelefon']) ?>" required>
                </label>
                <label>Sanatate Baterie
                    <input type="text" name="sanatate_baterie" value="<?= htmlspecialchars($telefon['SanatateBaterie']) ?>" required>
                </label>
                <label>Memorie (GB)
                    <input type="number" name="memorie" value="<?= htmlspecialchars($telefon['Memorie']) ?>" required>
                </label>
                <label>Culoare
                    <input type="text" name="culoare" value="<?= htmlspecialchars($telefon['Culoare']) ?>" required>
                </label>
                <label>Număr Telefon Vânzător
                    <input type="text" name="numar_telefon" value="<?= htmlspecialchars($telefon['NumarTelefon']) ?>" required>
                </label>
                <input type="hidden" name="telefon_id" value="<?= $telefon['IDTelefon'] ?>">
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
