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
$pages = get_menu_pages($user_id); // Obține paginile disponibile pentru utilizator

// Funcție pentru actualizarea adresei comenzii
if (isset($_POST['edit_adresa'])) {
    $id_comanda = $_POST['id_comanda'];
    $adresa_noua = $_POST['adresa'];

    $update_sql = "UPDATE Comenzi SET Adresa = ? WHERE IDComanda = ? AND IDUser = ?";
    $stmt = mysqli_prepare($db, $update_sql);
    mysqli_stmt_bind_param($stmt, 'sii', $adresa_noua, $id_comanda, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        // Actualizare reușită
    } else {
        echo "Eroare la actualizarea adresei comenzii: " . mysqli_error($db);
    }

    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Comenzile Mele</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
  <style>
    .order-table {
      margin-top: 30px;
    }
    .order-table table {
      width: 100%;
      border-collapse: collapse;
    }
    .order-table th, .order-table td {
      padding: 8px;
      border: 1px solid #ddd;
    }
    .order-table th {
      background-color: #f2f2f2;
    }
    .edit-form {
      margin: 0;
      padding: 0;
    }
    .edit-form input[type="text"] {
      width: 100%;
      box-sizing: border-box;
    }
  </style>
</head>
<body>

<?php include 'meniu.php'; ?>

<h2>Comenzile Mele</h2>

<?php
$sql = "SELECT c.IDComanda, c.Adresa, p.Nume AS NumeProdus, dc.Cantitate, dc.PretTotal, c.DataComanda 
        FROM Comenzi c 
        INNER JOIN DetaliiComenzi dc ON c.IDComanda = dc.IDComanda 
        INNER JOIN Produse p ON dc.IDProdus = p.IDProdus
        WHERE c.IDUser = $user_id";
$result = mysqli_query($db, $sql);

if (mysqli_num_rows($result) > 0) {
    echo '<div class="order-table">';
    echo '<table>
            <thead>
                <tr>
                    <th>ID Comandă</th>
                    <th>Nume Produs</th>
                    <th>Cantitate</th>
                    <th>Preț Total</th>
                    <th>Data Comandă</th>
                    <th>Adresa</th>
                    <th>Acțiuni</th>
                </tr>
            </thead>
            <tbody>';

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr id='order-{$row['IDComanda']}'>";
        echo "<td>" . $row["IDComanda"] . "</td>";
        echo "<td>" . $row["NumeProdus"] . "</td>";
        echo "<td>" . $row["Cantitate"] . "</td>";
        echo "<td>" . $row["PretTotal"] . "</td>";
        echo "<td>" . $row["DataComanda"] . "</td>";
        echo "<td>";
        echo "<form class='edit-form' method='post' action='comenzi-client.php'>";
        echo "<input type='hidden' name='id_comanda' value='{$row['IDComanda']}'>";
        echo "<input type='text' name='adresa' value='{$row['Adresa']}'>";
        echo "</td>";
        echo "<td>";
        echo "<button type='submit' class='button' name='edit_adresa'>Salvează</button>";
        echo "</form>";
        echo "<form id='delete-form-{$row['IDComanda']}' method='post'>";
        echo "<input type='hidden' name='id' value='{$row['IDComanda']}'>";
        echo "<button type='button' class='button alert delete-button' data-id='{$row['IDComanda']}'>Șterge</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }

    echo '</tbody>
          </table>';
    echo '</div>';
} else {
    echo "<p>Nu există comenzi disponibile.</p>";
}

mysqli_close($db);
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    $('.delete-button').on('click', function() {
      var id = $(this).data('id');
      if (confirm('Ești sigur că vrei să ștergi această comandă?')) {
        $.post('delete_comanda_client.php', { id: id }, function(response) {
          if (response === 'success') {
            alert('Comanda a fost ștearsă cu succes.');
            $('#order-' + id).remove(); // Șterge rândul din tabel
          } else {
            alert('Eroare la ștergerea comenzii.');
          }
        });
      }
    });
  });
</script>

</body>
</html>
