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

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

$product_id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($product_id)) {
    die("Produsul nu a fost specificat.");
}

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$pages = get_menu_pages($user_id); // Obține paginile disponibile pentru utilizator

// Procesează adăugarea produsului în coș
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Verifică dacă produsul există deja în coșul din baza de date pentru utilizatorul curent
    $check_sql = "SELECT * FROM Cos WHERE IDUser = '$user_id' AND IDProdus = '$product_id'";
    $check_result = mysqli_query($db, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        // Actualizează cantitatea dacă produsul există deja în coș
        $update_sql = "UPDATE Cos SET Cantitate = Cantitate + '$quantity'
                       WHERE IDUser = '$user_id' AND IDProdus = '$product_id'";
        mysqli_query($db, $update_sql);
    } else {
        // Inserează produsul în coș dacă nu există
        $insert_sql = "INSERT INTO Cos (IDUser, IDProdus, Cantitate)
                       VALUES ('$user_id', '$product_id', '$quantity')";
        mysqli_query($db, $insert_sql);
    }

    header("Location: detalii-telefon.php?id=$product_id&success=1");
    exit();
}

// Obține detaliile produsului din baza de date
$sql = "SELECT * FROM Produse WHERE IDProdus = '$product_id'";
$result = mysqli_query($db, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Produsul nu a fost găsit.");
}

$product = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalii Telefon</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
    <style>
        .product-details-container {
            margin-top: 20px;
        }
        .product-image {
            max-width: 100%;
            height: auto;
        }
        .product-info {
            padding-left: 20px;
        }
        .product-info h3 {
            margin-top: 0;
        }
        .product-info p {
            margin-bottom: 10px;
        }
        .product-info form {
            margin-top: 20px;
        }
        .callout.success {
            margin-top: 20px;
        }
        .thumbnail img {
            max-width: 250px;
            height: auto;
        }
        .benefits {
            background-color: #f9f9f9;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .benefits h4 {
            margin-top: 0;
            font-size: 20px;
            font-weight: bold;
            color: #007BFF;
        }
        .benefits p {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<?php include 'meniu.php'; ?>


<div class="grid-container">
    <div class="grid-x grid-padding-x product-details-container">
        <div class="medium-6 cell">
            <div class="thumbnail">
                <img src="<?= htmlspecialchars($product['Imagine']) ?>" alt="<?= htmlspecialchars($product['Nume']) ?>" class="product-image">
            </div>
        </div>
        <div class="medium-6 cell product-info">
            <h3><?= htmlspecialchars($product['Nume']) ?></h3>
            <p><strong>Condiție:</strong> <?= htmlspecialchars($product['Conditie']) ?></p>
            <p><strong>Baterie:</strong> <?= htmlspecialchars($product['Baterie']) ?></p>
            <p><strong>Culoare:</strong> <?= htmlspecialchars($product['Culoare']) ?></p>
            <p><strong>Preț:</strong> <?= htmlspecialchars($product['Pret']) ?> lei</p>
            <form action="detalii-telefon.php?id=<?= htmlspecialchars($product['IDProdus']) ?>" method="POST">
                <label>Cantitate:</label>
                <input type="number" name="quantity" value="1" min="1">
                <input type="submit" class="button" name="add_to_cart" value="Adaugă în Coș">
            </form>
            <?php if (isset($_GET['success'])): ?>
                <div class="callout success">
                    Produsul a fost adăugat în coș.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Benefits Section -->
    <div class="benefits">
        <h4>De ce să cumpărați de la noi?</h4>
        <p>La Amanet Telefoane vă oferim transport gratuit pentru toate comenzile și garantăm calitatea produselor noastre.</p>
        <p>Alegeți încrezători și bucurați-vă de experiența de cumpărare fără griji!</p>
    </div>
    <!-- End Benefits Section -->
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/js/foundation.min.js"></script>
<script>
    $(document).foundation();
</script>
</body>
</html>
