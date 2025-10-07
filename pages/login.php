<?php
session_start();
$db = mysqli_connect("127.0.0.1", "root", "");
mysqli_select_db($db, "WEB");

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $email = mysqli_real_escape_string($db, $email);
    $sql = "SELECT u.IDUser, u.Parola, u.type, ut.redirect_url FROM Users u JOIN user_types ut ON u.type = ut.id WHERE u.Email='$email'";
    $result = mysqli_query($db, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['Parola'])) {
            $_SESSION['user_id'] = $row['IDUser'];
            $_SESSION['user_type'] = $row['type'];
            $_SESSION['redirect_url'] = $row['redirect_url'];

            header("Location: " . $row['redirect_url']);
            exit();
        } else {
            $error_message = "Email sau parola incorecte.";
        }
    } else {
        $error_message = "Email sau parola incorecte.";
    }
}

mysqli_close($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.7.3/css/foundation.min.css">
    <script>
        function validateForm() {
            var email = document.forms["loginForm"]["email"].value;
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                alert("Te rog introdu un email valid.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<div class="top-bar">
    <div class="top-bar-left">
        <ul class="menu">
            <li><a href="despre.html">Acasă</a></li>
            
        </ul>
    </div>
</div>

<div class="grid-container">
    <div class="grid-x grid-padding-x align-center">
        <div class="medium-6 cell">
            <h3>Autentificare</h3>
            <?php if (!empty($error_message)): ?>
                <div class="callout alert">
                    <?= $error_message ?>
                </div>
            <?php endif; ?>
            <form name="loginForm" action="login.php" method="POST" onsubmit="return validateForm()">
                <label>Email:
                    <input type="text" name="email" required>
                </label>
                <label>Parola:
                    <input type="password" name="password" required>
                </label>
                <input type="submit" class="button" value="Autentificare">
            </form>
            <p class="text-center"><a href="register/register.html">Nu ai cont? Înregistrează-te</a></p>
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
