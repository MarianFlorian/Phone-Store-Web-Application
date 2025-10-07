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

// Verificare dacă s-a trimis formularul pentru ștergere
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_telefon'])) {
    $telefon_id = $_POST['telefon_id'];
    
    // Ștergere telefon din baza de date
    $delete_sql = "DELETE FROM TelefoaneVandute WHERE IDTelefon = $telefon_id AND IDUser = $user_id";
    if (mysqli_query($db, $delete_sql)) {
        echo displayAlert("Telefonul a fost șters cu succes.");
    } else {
        echo displayAlert("Eroare la ștergerea telefonului: " . mysqli_error($db), 'alert');
    }
}

// Interogare pentru a obține telefoanele vândute de utilizator
$sql = "SELECT * FROM TelefoaneVandute WHERE IDUser = $user_id";
$result = mysqli_query($db, $sql);
$telefoane_vandute = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $telefoane_vandute[] = $row;
    }
}


?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telefoane Vândute</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
    <style>
        .btn-danger {
            background-color: #cc4b37;
            color: white;
            
        }
        
    </style>
</head>
<body>

<?php include 'meniu.php'; ?>

<div class="grid-container">
    <div class="grid-x grid-padding-x">
        <div class="medium-12 cell">
            <h3>Telefoane Vândute de Utilizator</h3>
            <?php if (!empty($telefoane_vandute)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nume Telefon</th>
                            <th>Stare Telefon</th>
                            <th>Sanatate Baterie</th>
                            <th>Memorie (GB)</th>
                            <th>Culoare</th>
                            <th>Număr Telefon Vânzător</th>
                            <th>Data Adăugare</th>
                            <th>Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($telefoane_vandute as $telefon): ?>
                            <tr>
                                <td><?= htmlspecialchars($telefon['NumeTelefonVandut']) ?></td>
                                <td><?= htmlspecialchars($telefon['StareTelefon']) ?></td>
                                <td><?= htmlspecialchars($telefon['SanatateBaterie']) ?></td>
                                <td><?= htmlspecialchars($telefon['Memorie']) ?></td>
                                <td><?= htmlspecialchars($telefon['Culoare']) ?></td>
                                <td><?= htmlspecialchars($telefon['NumarTelefon']) ?></td>
                                <td><?= htmlspecialchars($telefon['DataAdaugare']) ?></td>
                                <td>
                                    <form method="post" action="vinde_edit.php">
                                        <input type="hidden" name="telefon_id" value="<?= $telefon['IDTelefon'] ?>">
                                        <button type="submit" name="edit_telefon" class="button">Editează</button>
                                    </form>
                                    <form method="post">
                                        <input type="hidden" name="telefon_id" value="<?= $telefon['IDTelefon'] ?>">
                                        <button type="submit" name="delete_telefon" class="button btn-danger">Șterge</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nu există telefoane vândute.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<a href="vinde_procesare.php" class="button">Vinde un nou telefon!</a>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/js/foundation.min.js"></script>
<script>
    $(document).foundation();
</script>
</body>
</html>
