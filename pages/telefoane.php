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
    <title>Telefoane de Vânzare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
    <style>
        .phone-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-top: 20px;
        }
        .phone-card {
            width: 300px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .phone-card img {
            max-width: 100%;
            height: auto;
        }
        .phone-card:hover {
            background-color: #f3f3f3;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .phone-card h4 {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .phone-card p {
            margin-bottom: 5px;
        }
        .button {
            background-color: #007BFF;
            color: white;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .search-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
        .search-container input[type=text] {
            padding: 10px;
            width: 300px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px 0 0 5px;
            outline: none;
        }
        .search-container button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: 1px solid #007BFF;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .search-container button:hover {
            background-color: #0056b3;
        }
        .filter-container {
            width: 250px;
            margin-top: 20px;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .filter-container h4 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .filter-container label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<?php include 'meniu.php'; ?>

<div class="grid-container">
    <div class="grid-x grid-padding-x">
        <div class="cell large-3">
            <div class="filter-container">
                <h4>Filtrează după preț</h4>
                <label>
                    <input type="checkbox" name="price" value="0-500"> 0 lei - 500 lei
                </label>
                <label>
                    <input type="checkbox" name="price" value="501-1000"> 501 lei - 1000 lei
                </label>
                <label>
                    <input type="checkbox" name="price" value="1001-1500"> 1001 lei - 1500 lei
                </label>
                <label>
                    <input type="checkbox" name="price" value="1501-2000"> 1501 lei - 2000 lei
                </label>
                <label>
                    <input type="checkbox" name="price" value="2001-"> Peste 2000 lei
                </label>

                <h4 style="margin-top: 20px;">Filtrează după categorie</h4>
                <label>
                    <input type="checkbox" name="category" value="iPhone"> iPhone
                </label>
                <label>
                    <input type="checkbox" name="category" value="Samsung"> Samsung
                </label>

                <button class="button" onclick="applyFilters()">Aplică filtre</button>
            </div>
        </div>
        <div class="cell large-9">
            <div class="search-container">
                <input type="text" id="search" placeholder="Caută telefon...">
                <button class="button" onclick="searchPhones()">Caută</button>
            </div>

            <div class="phone-container">
                <?php
                // Conectare la baza de date
                $db = mysqli_connect("127.0.0.1", "root", "", "WEB");

                $sql = "SELECT IDProdus, Nume, Pret, Imagine FROM Produse";
                $result = mysqli_query($db, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<div class="phone-card" onclick="location.href=\'detalii-telefon.php?id=' . $row['IDProdus'] . '\'">';
                        echo '<h4>' . $row['Nume'] . '</h4>';
                        echo '<img src="' . $row['Imagine'] . '" alt="' . $row['Nume'] . '">';
                        echo '<p>Preț: ' . $row['Pret'] . ' lei</p>';
                        echo '</div>';
                    }
                } else {
                    echo "<p>Nu sunt telefoane disponibile pentru vânzare în acest moment.</p>";
                }

                mysqli_close($db);
                ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/js/foundation.min.js"></script>
<script>
    $(document).foundation();

    function searchPhones() {
        var input, filter, container, cards, card, title, i;
        input = document.getElementById('search');
        filter = input.value.toUpperCase().replace(/\s/g, ''); // Convertim în majuscule și eliminăm spațiile
        container = document.querySelector('.phone-container'); // Selectăm containerul care conține cardurile

        cards = container.querySelectorAll('.phone-card'); // Selectăm toate cardurile

        cards.forEach(function(card) {
            title = card.querySelector('h4'); // Selectăm titlul din fiecare card
            var titleText = title.innerText.toUpperCase().replace(/\s/g, ''); // Convertim în majuscule și eliminăm spațiile
            if (titleText.includes(filter)) { // Verificăm dacă titlul conține textul căutat
                card.style.display = ""; // Afișăm cardul dacă găsim o potrivire
            } else {
                card.style.display = "none"; // Ascundem cardul dacă nu găsim o potrivire
            }
        });
    }

    function applyFilters() {
        var priceFilters = document.querySelectorAll('input[name=price]:checked');
        var categoryFilters = document.querySelectorAll('input[name=category]:checked');
        var container = document.querySelector('.phone-container');
        var cards = container.querySelectorAll('.phone-card');

        cards.forEach(function(card) {
            var price = parseFloat(card.querySelector('p').innerText.split(':')[1]); // Extrag prețul din textul paragrafului
            var category = card.querySelector('h4').innerText.trim().toUpperCase(); // Extrag categoria din titlul cardului și convertesc la majuscule

            var showCard = true;

            // Verificăm filtrele de preț
            priceFilters.forEach(function(filter) {
                var range = filter.value.split('-');
                var min = parseFloat(range[0].trim());
                var max = parseFloat(range[1].trim());

                if (price < min || price > max) {
                    showCard = false;
                }
            });

            // Verificăm filtrele de categorie
            categoryFilters.forEach(function(filter) {
                var filterValue = filter.value.toUpperCase();
                if (!category.includes(filterValue)) {
                    showCard = false;
                }
            });

            // Aplicăm afișarea ascunsă sau afișată în funcție de filtre
            if (showCard) {
                card.style.display = ""; // Afișăm cardul dacă respectă toate filtrele
            } else {
                card.style.display = "none"; // Ascundem cardul dacă nu respectă toate filtrele
            }
        });
    }
</script>

</body>
</html>
