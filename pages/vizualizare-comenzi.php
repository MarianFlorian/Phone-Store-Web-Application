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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vizualizare comenzi</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
  <style>
    .phone-table {
      margin-top: 30px;
    }
    .phone-table table {
      width: 100%;
      border-collapse: collapse;
    }
    .phone-table th, .phone-table td {
      padding: 8px;
      border: 1px solid #ddd;
    }
    .phone-table th {
      background-color: #f2f2f2;
    }
    .form-container {
      margin-top: 20px;
    }
  </style>
</head>
<body>

<?php include 'meniu.php'; ?>

<h2>Vizualizare Comenzi</h2>
</br>

<?php
$sql = "SELECT c.IDComanda, p.Nume AS NumeProdus, dc.Cantitate, dc.PretTotal, c.DataComanda 
        FROM Comenzi c 
        INNER JOIN DetaliiComenzi dc ON c.IDComanda = dc.IDComanda 
        INNER JOIN Produse p ON dc.IDProdus = p.IDProdus";
$result = mysqli_query($db, $sql);

if (mysqli_num_rows($result) > 0) {
    echo '<div class="phone-table">';
    echo '<table>
            <thead>
                <tr>
                    <th>ID Comandă</th>
                    <th>Nume Produs</th>
                    <th>Cantitate</th>
                    <th>Preț Total</th>
                    <th>Data Comandă</th>
                    <th>Acțiuni</th>
                </tr>
            </thead>
            <tbody>';

    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row["IDComanda"] . "</td>";
        echo "<td>" . $row["NumeProdus"] . "</td>";
        echo "<td>" . $row["Cantitate"] . "</td>";
        echo "<td>" . $row["PretTotal"] . "</td>";
        echo "<td>" . $row["DataComanda"] . "</td>";
        echo '<td>
                <button class="button edit-button" data-id="' . $row["IDComanda"] . '" data-nume="' . $row["NumeProdus"] . '" data-cantitate="' . $row["Cantitate"] . '" data-prettotal="' . $row["PretTotal"] . '" data-datacomanda="' . $row["DataComanda"] . '">Editează</button>
                <button class="button alert delete-button" data-id="' . $row["IDComanda"] . '">Șterge</button>
              </td>';
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

<!-- Modal pentru editare -->
<div class="reveal" id="editModal" data-reveal>
  <h2>Editează Comanda</h2>
  <form id="editForm" method="POST" action="update_comanda.php">
    <input type="hidden" id="edit-id" name="id">
    <label for="edit-nume">Nume Produs:</label>
    <input type="text" id="edit-nume" name="nume" readonly>
    <label for="edit-cantitate">Cantitate:</label>
    <input type="number" id="edit-cantitate" name="cantitate" required>
    <label for="edit-prettotal">Preț Total:</label>
    <input type="number" id="edit-prettotal" name="prettotal" required>
    <label for="edit-datacomanda">Data Comandă:</label>
    <input type="date" id="edit-datacomanda" name="datacomanda" required>
    <button type="submit" class="button">Salvează</button>
  </form>
  <button class="close-button" data-close aria-label="Close modal" type="button">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="container">
    
    <div class="button-container">
        <a href="vizualizare-comenzi.php" class="button">Vizualizare</a>
        <a href="adaugare-comenzi.php" class="button">Adaugă</a>
        <a href="cautare-comenzi.html" class="button">Caută</a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/js/foundation.min.js"></script>
<script>
  $(document).foundation();

  $(document).ready(function() {
    $('.edit-button').on('click', function() {
      var id = $(this).data('id');
      var nume = $(this).data('nume');
      var cantitate = $(this).data('cantitate');
      var prettotal = $(this).data('prettotal');
      var datacomanda = $(this).data('datacomanda');
      
      $('#edit-id').val(id);
      $('#edit-nume').val(nume);
      $('#edit-cantitate').val(cantitate);
      $('#edit-prettotal').val(prettotal);
      $('#edit-datacomanda').val(datacomanda);
      
      $('#editModal').foundation('open');
    });

    $('.delete-button').on('click', function() {
      var id = $(this).data('id');
      if (confirm('Ești sigur că vrei să ștergi această comandă?')) {
        $.ajax({
          url: 'delete_comanda.php',
          type: 'POST',
          data: { id: id },
          success: function(response) {
            if (response == 'success') {
              location.reload();
            } else {
              alert('Eroare la ștergerea comenzii.');
            }
          }
        });
      }
    });
  });
</script>
</body>
</html>
