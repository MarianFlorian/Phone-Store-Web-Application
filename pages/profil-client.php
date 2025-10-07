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

// Obține datele utilizatorului
$sql = "SELECT Nume, Prenume, Email, Parola FROM Users WHERE IDUser = '$user_id'";
$result = mysqli_query($db, $sql);

if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
} else {
    $error_message = "A apărut o eroare la preluarea datelor utilizatorului.";
}

// Actualizează datele utilizatorului
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $nume = $_POST['nume'];
    $prenume = $_POST['prenume'];
    $email = $_POST['email'];

    $nume = mysqli_real_escape_string($db, $nume);
    $prenume = mysqli_real_escape_string($db, $prenume);
    $email = mysqli_real_escape_string($db, $email);

    $update_sql = "UPDATE Users SET Nume='$nume', Prenume='$prenume', Email='$email' WHERE IDUser='$user_id'";
    if (mysqli_query($db, $update_sql)) {
        $success_message = "Profilul a fost actualizat cu succes.";
    } else {
        $error_message = "A apărut o eroare la actualizarea profilului: " . mysqli_error($db);
    }
}

// Schimbarea parolei
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    $current_password = mysqli_real_escape_string($db, $current_password);
    $new_password = mysqli_real_escape_string($db, $new_password);

    // Verifică parola curentă
    if (password_verify($current_password, $user['Parola'])) {
        // Hash-uiește și actualizează parola nouă
        $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $update_password_sql = "UPDATE Users SET Parola='$new_password_hashed' WHERE IDUser='$user_id'";
        if (mysqli_query($db, $update_password_sql)) {
            $success_message = "Parola a fost actualizată cu succes.";
        } else {
            $error_message = "A apărut o eroare la actualizarea parolei: " . mysqli_error($db);
        }
    } else {
        $error_message = "Parola curentă nu este corectă.";
    }
}

// Ștergerea contului
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_account'])) {
    $delete_sql = "DELETE FROM Users WHERE IDUser = '$user_id'";
    if (mysqli_query($db, $delete_sql)) {
        // Șterge sesiunea și redirecționează utilizatorul la pagina de login
        session_destroy();
        header("Location: login.php");
        exit();
    } else {
        $error_message = "A apărut o eroare la ștergerea contului: " . mysqli_error($db);
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Client</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
</head>
<body>

<!-- Include meniul -->
<?php include 'meniu.php'; ?>
<!-- End Top Bar -->

<div class="grid-container">
    <div class="grid-x grid-padding-x">
        <div class="medium-12 cell">
            <h3>Profilul Meu</h3>
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
            <form action="profil-client.php" method="POST">
                <label>Nume:
                    <input type="text" name="nume" value="<?= htmlspecialchars($user['Nume']) ?>" required>
                </label>
                <label>Prenume:
                    <input type="text" name="prenume" value="<?= htmlspecialchars($user['Prenume']) ?>" required>
                </label>
                <label>Email:
                    <input type="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" required>
                </label>
                <input type="submit" class="button" name="update_profile" value="Actualizează Profilul">
            </form>

            <hr>

            <h3>Schimbă Parola</h3>
            <form action="profil-client.php" method="POST">
                <label>Parola curentă:
                    <input type="password" name="current_password" required>
                </label>
                <label>Parola nouă:
                    <input type="password" name="new_password" required>
                </label>
                <input type="submit" class="button" name="change_password" value="Schimbă Parola">
            </form>

            <hr>

            <h3>Șterge Contul</h3>
            <form action="profil-client.php" method="POST" onsubmit="return confirm('Ești sigur că vrei să ștergi contul? Această acțiune nu poate fi anulată.');">
                <input type="submit" class="button alert" name="delete_account" value="Șterge Contul">
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
