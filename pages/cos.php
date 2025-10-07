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

// Procesează actualizarea cantității
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Actualizează cantitatea în baza de date
    $update_sql = "UPDATE Cos SET Cantitate = '$quantity' WHERE IDUser = '$user_id' AND IDProdus = '$product_id'";
    mysqli_query($db, $update_sql);

    header("Location: cos.php");
    exit();
}

// Procesează ștergerea produsului din coș
if (isset($_GET['delete']) && $_GET['delete'] == 1 && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Șterge produsul din baza de date
    $delete_sql = "DELETE FROM Cos WHERE IDUser = '$user_id' AND IDProdus = '$product_id'";
    mysqli_query($db, $delete_sql);

    header("Location: cos.php");
    exit();
}

// Procesează plasarea comenzii
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    $adresa = $_POST['adresa'];
    $adresa = mysqli_real_escape_string($db, $adresa);

    // Începe tranzacția pentru a asigura integritatea datelor
    mysqli_begin_transaction($db);

    // Inserează comanda în tabelul Comenzi
    $insert_comanda_sql = "INSERT INTO Comenzi (IDUser, Adresa, DataComanda) 
                           VALUES ('$user_id', '$adresa', NOW())";
    if (mysqli_query($db, $insert_comanda_sql)) {
        $id_comanda = mysqli_insert_id($db);

        // Înscrie detalii despre produse în tabelul DetaliiComenzi și șterge produsele din Cos
        $cos_sql = "SELECT * FROM Cos WHERE IDUser = '$user_id'";
        $cos_result = mysqli_query($db, $cos_sql);

        while ($row = mysqli_fetch_assoc($cos_result)) {
            $product_id = $row['IDProdus'];
            $quantity = $row['Cantitate'];

            // Obține detaliile despre produs din tabelul Produse
            $produs_sql = "SELECT Pret FROM Produse WHERE IDProdus = '$product_id'";
            $produs_result = mysqli_query($db, $produs_sql);
            $produs = mysqli_fetch_assoc($produs_result);

            if ($produs) {
                $pret_unitar = $produs['Pret'];
                $pret_total = $pret_unitar * $quantity;

                // Inserează detalii în tabelul DetaliiComenzi
                $insert_detalii_sql = "INSERT INTO DetaliiComenzi (IDComanda, IDProdus, Cantitate, PretTotal)
                                       VALUES ('$id_comanda', '$product_id', '$quantity', '$pret_total')";
                mysqli_query($db, $insert_detalii_sql);
            } else {
                // Dacă nu se găsește produsul, anulează tranzacția și afișează eroarea
                $error_message = "Produsul cu ID-ul $product_id nu a fost găsit în baza de date.";
                mysqli_rollback($db);
                break;
            }
        }

        // Șterge produsele din Cos după plasarea comenzii
        $delete_cos_sql = "DELETE FROM Cos WHERE IDUser = '$user_id'";
        mysqli_query($db, $delete_cos_sql);

        // Verifică dacă nu există erori și finalizează tranzacția
        if (empty($error_message)) {
            mysqli_commit($db);

            // Afișează mesajul de succes pentru plasarea comenzii
            $success_message = "Comanda a fost plasată cu succes.";

            // Poți redirecționa utilizatorul sau afișa altceva după plasarea comenzii
            // header("Location: cos.php");
            // exit();
        }
    } else {
        // Afișează eroarea în cazul unei probleme la inserarea comenzii
        $error_message = "A apărut o eroare la plasarea comenzii: " . mysqli_error($db);
        mysqli_rollback($db);
    }
}

// Obține produsele din coș pentru afișare
$products = [];
$total_general = 0;

$sql = "SELECT c.IDProdus, p.Nume, p.Pret, c.Cantitate
        FROM Cos c
        JOIN Produse p ON c.IDProdus = p.IDProdus
        WHERE c.IDUser = '$user_id'";
$result = mysqli_query($db, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
    $total_general += $row['Pret'] * $row['Cantitate'];
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coș de Cumpărături</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
</head>
<body>

<?php include 'meniu.php'; ?>

<div class="grid-container">
    <div class="grid-x grid-padding-x">
        <div class="medium-12 cell">
            <h3>Coș de Cumpărături</h3>
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
            <table>
                <thead>
                    <tr>
                        <th>Produs</th>
                        <th>Preț</th>
                        <th>Cantitate</th>
                        <th>Total</th>
                        <th>Acțiuni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <?php $total = $product['Pret'] * $product['Cantitate']; ?>
                        <tr>
                            <td><?= htmlspecialchars($product['Nume']) ?></td>
                            <td><?= htmlspecialchars($product['Pret']) ?> lei</td>
                            <td>
                                <form action="cos.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?= $product['IDProdus'] ?>">
                                    <input type="number" name="quantity" value="<?= $product['Cantitate'] ?>" min="1">
                                    <input type="submit" class="button tiny" name="update_quantity" value="Actualizează">
                                </form>
                            </td>
                            <td><?= htmlspecialchars($total) ?> lei</td>
                            <td>
                                <a href="cos.php?delete=1&product_id=<?= $product['IDProdus'] ?>" class="button tiny alert">Șterge</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" style="text-align: right;">Total General:</td>
                        <td><?= htmlspecialchars($total_general) ?> lei</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <form action="cos.php" method="POST">
                <label>Adresă de livrare:
                    <input type="text" name="adresa" required>
                </label>
                <input type="submit" class="button" name="place_order" value="Plasează Comanda">
            </form>
            <a href="telefoane.php" class="button">Continuă Cumpărăturile</a>
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
