<?php
// Verifică dacă sesiunea a fost deja pornită
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
$pages = get_menu_pages($user_id); // Obține paginile disponibile pentru utilizator
?>

<!-- Start Top Bar -->
<div class="top-bar">
  <div class="top-bar-left">
    <ul class="menu">
      <li>Amanet Telefoane</li>
      <!-- Afisarea paginilor din meniu -->
      <?php foreach ($pages as $page): ?>
        <li><a href="<?php echo $page['url']; ?>"><?php echo $page['name']; ?></a></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <div class="top-bar-right">
    <ul class="menu">
      
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>
</div>
<!-- End Top Bar -->
