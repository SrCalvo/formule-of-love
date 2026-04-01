<?php
require_once "../config/db.php";
session_start();

// PROTECCIÓN
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$success_song = "";
$success_dedication = "";

// AGREGAR CANCIÓN
if (isset($_POST["add_song"])) {
    $link = trim($_POST["youtube_link"]);
    $title = trim($_POST["title"]);
    
    if ($title == "") {
        $title = "Canción sin título";
    }

    if ($link != "") {
        $stmt = $conn->prepare("INSERT INTO songs (youtube_link, title) VALUES (?, ?)");
        $stmt->bind_param("ss", $link, $title);
        if ($stmt->execute()) {
            $success_song = "✅ Canción agregada correctamente";
        }
    }
}

// CREAR DEDICATORIA
if (isset($_POST["add_dedication"])) {
    $song_id = intval($_POST["song_id"]);
    $message = trim($_POST["message"]);

    if ($song_id > 0 && $message != "") {
        $conn->query("UPDATE dedications SET is_active = 0");

        $stmt = $conn->prepare("
            INSERT INTO dedications (song_id, message, is_active)
            VALUES (?, ?, 1)
        ");
        $stmt->bind_param("is", $song_id, $message);
        if ($stmt->execute()) {
            $success_dedication = "✅ Dedicatoria publicada correctamente";
        }
    }
}

// Obtener canciones
$songs = $conn->query("SELECT id, title FROM songs ORDER BY id DESC");

// Historial
$history = $conn->query("
    SELECT d.message, s.youtube_link, d.created_at, s.title
    FROM dedications d
    JOIN songs s ON d.song_id = s.id
    ORDER BY d.created_at DESC
    LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Panel de Control 💖</title>
    <link href="css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
</head>
<body class="dashboard-page">
    <div class="dashboard-container">
        <!-- Navbar -->
        <nav class="dashboard-nav">
            <div class="nav-title">
                <span>💖</span>
                <span>Panel de Control</span>
            </div>
            <a href="logout.php" class="logout-btn">
                <span>🚪</span>
                <span>Cerrar Sesión</span>
            </a>
        </nav>

        <!-- Grid de tarjetas -->
        <div class="dashboard-grid">
            <!-- Agregar Canción -->
            <div class="dashboard-card">
                <div class="card-header">
                    <span class="card-icon">🎵</span>
                    <h3>Agregar Canción</h3>
                </div>
                
                <?php if ($success_song != ""): ?>
                <div style="background: #d1fae5; color: #065f46; padding: 0.75rem; border-radius: 12px; margin-bottom: 1rem;">
                    <?php echo $success_song; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Título de la canción</label>
                        <input type="text" 
                               name="title" 
                               class="form-input" 
                               placeholder="Ej: Mi Canción Favorita">
                    </div>
                    
                    <div class="form-group">
                        <label>Link de YouTube *</label>
                        <input type="url" 
                               name="youtube_link" 
                               class="form-input" 
                               placeholder="https://youtube.com/watch?v=..." 
                               required>
                    </div>
                    
                    <button type="submit" name="add_song" class="submit-btn">
                        💾 Guardar Canción
                    </button>
                </form>
            </div>

            <!-- Nueva Dedicatoria -->
            <div class="dashboard-card">
                <div class="card-header">
                    <span class="card-icon">💌</span>
                    <h3>Nueva Dedicatoria</h3>
                </div>
                
                <?php if ($success_dedication != ""): ?>
                <div style="background: #d1fae5; color: #065f46; padding: 0.75rem; border-radius: 12px; margin-bottom: 1rem;">
                    <?php echo $success_dedication; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Seleccionar Canción</label>
                        <select name="song_id" class="form-select" required>
                            <option value="">Selecciona una canción</option>
                            <?php while($row = $songs->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php 
                                $title = $row['title'];
                                if ($title == "") {
                                    $title = "Canción sin título";
                                }
                                echo htmlspecialchars($title); 
                                ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Mensaje de Dedicatoria *</label>
                        <textarea name="message" 
                                  class="form-textarea" 
                                  placeholder="Escribe un mensaje especial..." 
                                  required></textarea>
                    </div>
                    
                    <button type="submit" name="add_dedication" class="submit-btn">
                        💖 Publicar Dedicatoria
                    </button>
                </form>
            </div>
        </div>

        <!-- Historial Reciente -->
        <div class="history-section">
            <div class="history-title">
                <span>📜</span>
                <span>Historial Reciente</span>
            </div>
            
            <div class="history-list">
                <?php if ($history->num_rows > 0): ?>
                    <?php while($row = $history->fetch_assoc()): ?>
                    <div class="history-item">
                        <div class="history-date">
                            <span>📅</span>
                            <span><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></span>
                        </div>
                        <div class="history-message">
                            <?php 
                            $message = htmlspecialchars($row['message']);
                            if (strlen($message) > 100) {
                                echo substr($message, 0, 100) . '...';
                            } else {
                                echo $message;
                            }
                            ?>
                        </div>
                        <a href="<?php echo htmlspecialchars($row['youtube_link']); ?>" 
                           class="history-link" 
                           target="_blank">
                            <span>🎬</span>
                            <span>Ver canción</span>
                        </a>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 2rem; color: #94a3b8;">
                        <span>💔</span>
                        <p>Todavía no hay dedicatorias</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>