<?php
session_start();
$db = mysqli_connect("127.0.0.1", "root", "", "WEB");

function has_access($user_id, $page_url) {
    global $db;

    // Obține ID-ul paginii din URL
    $sql = "SELECT Id FROM pagini WHERE pagina = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "s", $page_url);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $page_id = $row['Id'];

        // Verifică dacă utilizatorul are drepturi pentru această pagină
        $sql = "SELECT * FROM drepturi WHERE IdUser = ? AND IdPage = ?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $page_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return mysqli_num_rows($result) > 0;
    }

    return false;
}

// Exemplu de utilizare:
$page_url = basename($_SERVER['PHP_SELF']); // Obține URL-ul paginii curente

if (!isset($_SESSION['user_id']) || !has_access($_SESSION['user_id'], $page_url)) {
    header("Location: login.php");
    exit();
}
?>
