<?php
// access_functions.php

require_once 'db_connection.php'; // Include fișierul de conexiune la baza de date

function has_access($user_id, $page_id) {
    global $db;

    $sql = "SELECT COUNT(*) AS count FROM drepturi WHERE user_type = (
        SELECT type FROM Users WHERE IDUser = ?
    ) AND IdPage = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $page_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $row = mysqli_fetch_assoc($result);
    return $row['count'] > 0;
}

function get_menu_pages($user_id) {
    global $db;

    $sql = "SELECT * FROM pagini WHERE Meniu = 1";
    $result = mysqli_query($db, $sql);

    $pages = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $page_id = $row['Id'];
        $page_name = $row['nume_meniu'];
        $page_url = $row['pagina'];

        if (has_access($user_id, $page_id)) {
            $pages[] = [
                'name' => $page_name,
                'url' => $page_url
            ];
        }
    }

    return $pages;
}

function check_user_access($user_id, $page) {
    global $db;

    // Obține tipul utilizatorului
    $sql = "SELECT type FROM Users WHERE IDUser = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        return false; // Utilizatorul nu există
    }

    // Obține ID-ul paginii din URL
    $sql = "SELECT Id FROM pagini WHERE pagina = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "s", $page);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $page_row = mysqli_fetch_assoc($result);

    if (!$page_row) {
        return false; // Pagina nu există
    }

    $page_id = $page_row['Id'];

    // Verifică dacă utilizatorul are drepturi pentru această pagină
    $sql = "SELECT * FROM drepturi WHERE IdPage = ? AND user_type = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $page_id, $user['type']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_num_rows($result) > 0;
}
?>
