<?php
session_start();

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


// Funcție pentru a afișa mesajele de succes sau de eroare
function displayAlert($message, $type = 'success')
{
    return '<div class="callout ' . $type . '">' . $message . '</div>';
}

// Verificare dacă s-a trimis cererea de ștergere
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_telefon'])) {
    $telefon_id = $_POST['telefon_id'];
    
    // Ștergere telefon din baza de date
    $delete_sql = "DELETE FROM TelefoaneVandute WHERE IDTelefon = $telefon_id";
    
    if (mysqli_query($db, $delete_sql)) {
        echo displayAlert("Telefonul a fost șters cu succes.");
    } else {
        echo displayAlert("Eroare la ștergerea telefonului: " . mysqli_error($db), 'alert');
    }
}

// Preluare toate telefoanele vândute de toți utilizatorii
$sql = "SELECT t.IDTelefon, t.NumeTelefonVandut, t.StareTelefon, t.SanatateBaterie, 
               t.Memorie, t.Culoare, t.NumarTelefon, t.DataAdaugare, t.IDUser, u.Email 
        FROM TelefoaneVandute t
        LEFT JOIN Users u ON t.IDUser = u.IDUser";
$result = mysqli_query($db, $sql);

$telefoane = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $telefoane[] = $row;
    }
} else {
    echo displayAlert("Eroare la preluarea telefoanelor: " . mysqli_error($db), 'alert');
}

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telefoane Vândute</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
</head>
<body>

<?php include 'meniu.php'; ?>

<div class="grid-container">
    <div class="grid-x grid-padding-x">
        <div class="medium-12 cell">
            <h3>Telefoane Vândute</h3>
            <?php if (!empty($telefoane)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID Telefon</th>
                            <th>ID Utilizator</th>
                            <th>Email Utilizator</th>
                            <th>Nume Telefon</th>
                            <th>Stare Telefon</th>
                            <th>Sănătate Baterie</th>
                            <th>Memorie (GB)</th>
                            <th>Culoare</th>
                            <th>Număr Telefon Vânzător</th>
                            <th>Data Adăugare</th>
                            <th>Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($telefoane as $telefon): ?>
                            <tr>
                                <td><?= htmlspecialchars($telefon['IDTelefon']) ?></td>
                                <td><?= htmlspecialchars($telefon['IDUser']) ?></td>
                                <td><?= htmlspecialchars($telefon['Email']) ?></td>
                                <td><?= htmlspecialchars($telefon['NumeTelefonVandut']) ?></td>
                                <td><?= htmlspecialchars($telefon['StareTelefon']) ?></td>
                                <td><?= htmlspecialchars($telefon['SanatateBaterie']) ?></td>
                                <td><?= htmlspecialchars($telefon['Memorie']) ?></td>
                                <td><?= htmlspecialchars($telefon['Culoare']) ?></td>
                                <td><?= htmlspecialchars($telefon['NumarTelefon']) ?></td>
                                <td><?= htmlspecialchars($telefon['DataAdaugare']) ?></td>
                                <td>
                                    <form method="post" action="vinde_admin.php" onsubmit="return confirm('Ești sigur că vrei să ștergi acest telefon?');">
                                        <input type="hidden" name="telefon_id" value="<?= $telefon['IDTelefon'] ?>">
                                        <input type="submit" class="button alert" name="delete_telefon" value="Șterge">
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/js/foundation.min.js"></script>
<script>
    $(document).foundation();
</script>
</body>
</html>
