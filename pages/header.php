<?php
session_start();
$db = mysqli_connect("127.0.0.1", "root", "");
mysqli_select_db($db, "WEB");

if (!isset($_SESSION['user_id'])) {
    // Utilizator neautentificat
    echo '
    <div class="top-bar">
        <div class="top-bar-left">
            <ul class="menu">
                <li><a href="index.php">Amanet Telefoane</a></li>
            </ul>
        </div>
        <div class="top-bar-right">
            <ul class="menu">
                <li><a href="cos.html">Cos</a></li>
                <li><a href="autentificare.html">Autentificare</a></li>
            </ul>
        </div>
    </div>
    ';
} else {
    // Utilizator autentificat, afișăm meniul corespunzător tipului de utilizator

    $user_id = $_SESSION['user_id'];

    // Interogare pentru a afla tipul utilizatorului curent
    $query = "SELECT type FROM Users WHERE IDUser = '$user_id'";
    $result = mysqli_query($db, $query);
    if (!$result) {
        die("Eroare la interogare: " . mysqli_error($db));
    }

    $row = mysqli_fetch_assoc($result);
    $user_type_id = $row['type'];

    // Interogare pentru a afla paginile accesibile pentru tipul de utilizator
    $pages_query = "SELECT p.nume_meniu, p.pagina
                    FROM pagini p
                    INNER JOIN drepturi d ON p.Id = d.IdPage
                    WHERE d.IdUser = '$user_id'";
    $pages_result = mysqli_query($db, $pages_query);
    if (!$pages_result) {
        die("Eroare la interogare: " . mysqli_error($db));
    }

    // Construim meniul bazat pe paginile accesibile pentru utilizatorul curent
    echo '
    <div class="top-bar">
        <div class="top-bar-left">
            <ul class="menu">
                <li><a href="index.php">Amanet Telefoane</a></li>';

    while ($page_row = mysqli_fetch_assoc($pages_result)) {
        echo '<li><a href="' . $page_row['pagina'] . '">' . $page_row['nume_meniu'] . '</a></li>';
    }

    echo '
            </ul>
        </div>
        <div class="top-bar-right">
            <ul class="menu">
                <li><a href="cos.html">Cos</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
    ';
}

mysqli_close($db);
?>
