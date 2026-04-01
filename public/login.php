<?php
require_once "../config/db.php";
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        $error = "❌ Todos los campos son obligatorios";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && hash('sha256', $password) === $user["password"]) {
            $_SESSION["user"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "❌ Usuario o contraseña incorrectos";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Panel Administrativo 💖</title>
    <link href="css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">💖</div>
                <h2>Bienvenido</h2>
                <p class="login-subtitle">Accede al panel de administración</p>
            </div>

            <?php if ($error != ""): ?>
            <div class="error-message">
                <span>⚠️</span>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <div class="input-group">
                    <span class="input-icon">👤</span>
                    <input type="text" 
                           name="username" 
                           class="login-input" 
                           placeholder="Usuario" 
                           required 
                           autofocus>
                </div>

                <div class="input-group">
                    <span class="input-icon">🔒</span>
                    <input type="password" 
                           name="password" 
                           class="login-input" 
                           placeholder="Contraseña" 
                           required>
                </div>

                <button type="submit" class="login-btn">
                    Iniciar Sesión
                </button>
            </form>

            <div class="back-link">
                <a href="index.php">
                    <span>←</span>
                    <span>Volver al inicio</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>