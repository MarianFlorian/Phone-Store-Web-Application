<!DOCTYPE html>
<html class="no-js" lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Title...</title>
  <link rel="stylesheet" href="css/foundation.css">
  <link rel="stylesheet" href="css/app.css">
</head>
<body>

<?php
// Configurarea conexiunii la baza de date
$host = "127.0.0.1";
$username = "root";
$password = "";
$database = "WEB";

// Conectarea la baza de date
$db = mysqli_connect($host, $username, $password, $database);
if (!$db) {
    die("Conexiunea la baza de date a eșuat: " . mysqli_connect_error());
}

// Verificăm dacă utilizatorul este autentificat și are o sesiune activă
session_start();
if (!isset($_SESSION["user_id"]) || empty($_SESSION["user_id"])) {
    // Dacă utilizatorul nu este autentificat, îl redirecționăm către pagina de autentificare
    redirect("index.php");
} else {
    // Altfel, afișăm meniul dinamic pentru utilizatorul autentificat

    $page = basename($_SERVER['PHP_SELF']); // Numele paginii curente
    $userId = $_SESSION["user_id"]; // ID-ul utilizatorului din sesiune

    // Interogare pentru a selecta paginile la care utilizatorul are acces
    $query = "SELECT pagini.Meniu, pagini.nume_meniu, pagini.pagina FROM pagini INNER JOIN drepturi ON drepturi.IdPage = pagini.Id WHERE drepturi.IdUser = '$userId'";
    $result = mysqli_query($db, $query);

    // Verificăm dacă există rezultate în interogare
    if (mysqli_num_rows($result) > 0) {
        echo "<ul>";
        while ($row = mysqli_fetch_assoc($result)) {
            // Verificăm dacă pagina curentă este inclusă în meniu
            if ($row["pagina"] == $page) {
                echo "<li class='active'><a href='" . $row["pagina"] . "'>" . $row["nume_meniu"] . "</a></li>";
            } else {
                echo "<li><a href='" . $row["pagina"] . "'>" . $row["nume_meniu"] . "</a></li>";
            }
        }
        echo "</ul>";
    } else {
        // Dacă nu există drepturi definite pentru utilizatorul curent, îl redirecționăm la deconectare
        redirect("logout.php");
    }
}

// Funcție pentru redirecționare către o altă pagină
function redirect($url)
{
    header("Location: " . $url);
    exit();
}
?>

</body>
</html>
