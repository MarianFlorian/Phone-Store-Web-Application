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

$error_message = "";
$success_message = "";


// Procesare formular la trimiterea datelor
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idProdus = mysqli_real_escape_string($db, $_POST['idProdus']);
    $nume = mysqli_real_escape_string($db, $_POST['Nume']);
    $cantitate = mysqli_real_escape_string($db, $_POST['cantitate']);
    $pretTotal = mysqli_real_escape_string($db, $_POST['pretTotal']);
    $dataComanda = mysqli_real_escape_string($db, $_POST['dataComanda']);
    $adresa = mysqli_real_escape_string($db, $_POST['adresa']);
    $selected_user_id = mysqli_real_escape_string($db, $_POST['selected_user_id']);

    // Verifică dacă câmpurile sunt completate
    if (empty($idProdus) || empty($nume) || empty($cantitate) || empty($pretTotal) || empty($dataComanda) || empty($adresa) || empty($selected_user_id)) {
        $error_message = "Toate câmpurile sunt obligatorii.";
    } else {
        // Formatează data și ora în formatul acceptat de MySQL
        $dateTime = date('Y-m-d H:i:s', strtotime($dataComanda));

        // Inserare comandă în tabelul Comenzi
        $sql_comenzi = "INSERT INTO Comenzi (IDUser, IDProdus, Nume, PretTotal, Adresa, DataComanda, Cantitate) 
                        VALUES ('$selected_user_id', '$idProdus', '$nume', $pretTotal', '$adresa', '$dateTime','$cantitate' )";

        if (mysqli_query($db, $sql_comenzi)) {
            $idComanda = mysqli_insert_id($db); // Obține ID-ul comenzii inserate

            // Inserare detalii comandă în tabelul DetaliiComenzi
            $sql_detalii = "INSERT INTO DetaliiComenzi (IDComanda, IDProdus, Cantitate, PretTotal) 
                            VALUES ('$idComanda', '$idProdus', '$cantitate', '$pretTotal')";

            if (mysqli_query($db, $sql_detalii)) {
                $success_message = "Comanda a fost adăugată cu succes!";
            } else {
                $error_message = "Eroare la adăugarea detaliilor comenzii: " . mysqli_error($db);
                // Șterge comanda din tabelul Comenzi dacă există erori la inserarea în DetaliiComenzi
                $sql_delete_comanda = "DELETE FROM Comenzi WHERE IDComanda = '$idComanda'";
                mysqli_query($db, $sql_delete_comanda);
            }
        } else {
            $error_message = "Eroare la adăugarea comenzii: " . mysqli_error($db);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adăugare Comandă</title>

    <!-- Include Foundation CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">

    <style>
        .form-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-container h2 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .form-container .alert {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 3px;
        }
        .form-container label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .form-container input[type="text"],
        .form-container input[type="number"],
        .form-container input[type="datetime-local"] {
            width: calc(100% - 20px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .form-container select {
            width: calc(100% - 20px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .form-container input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 3px;
        }
        .form-container input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<?php include 'meniu.php'; ?>

<div class="form-container">
    <h2>Adăugare Comandă</h2>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php elseif (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <form method="POST" action="adaugare-comenzi.php">
        <label for="selected_user_id">ID Utilizator:</label>
        <input type="text" id="selected_user_id" name="selected_user_id" required>

        <label for="idProdus">ID Produs:</label>
        <input type="text" id="idProdus" name="idProdus" required>

        <label for="Nume">Nume:</label>
        <input type="text" id="Nume" name="Nume" required>

        <label for="cantitate">Cantitate:</label>
        <input type="number" id="cantitate" name="cantitate" min="1" required>

        <label for="pretTotal">Preț Total:</label>
        <input type="number" id="pretTotal" name="pretTotal" min="0" required>

        <label for="dataComanda">Data și Ora Comandă:</label>
        <input type="datetime-local" id="dataComanda" name="dataComanda" required>

        <label for="adresa">Adresă:</label>
        <input type="text" id="adresa" name="adresa" required>

        <input type="submit" class="button" value="Adaugă comandă">
    </form>
</div>

<!-- Include Foundation JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/js/foundation.min.js"></script>
<script>
    $(document).foundation();
</script>

</body>
</html>

<?php
?>
