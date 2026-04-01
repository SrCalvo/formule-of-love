<?php
require_once "../config/db.php";

// Configuración
$pageTitle = "💖 Mensaje Especial para Ti";

// Consulta segura
$stmt = $conn->prepare("
    SELECT d.message, s.youtube_link, s.title, d.created_at
    FROM dedications d
    JOIN songs s ON d.song_id = s.id
    WHERE d.is_active = 1
    ORDER BY d.created_at DESC
    LIMIT 1
");

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
} else {
    $data = null;
}

// Si no hay datos activos, buscar la última dedicatoria
if (!$data) {
    $stmt2 = $conn->prepare("
        SELECT d.message, s.youtube_link, s.title, d.created_at
        FROM dedications d
        JOIN songs s ON d.song_id = s.id
        ORDER BY d.created_at DESC
        LIMIT 1
    ");
    if ($stmt2) {
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $data = $result2->fetch_assoc();
    }
    
    if (!$data) {
        $data = array(
            "message" => "✨ Pronto habrá un mensaje especial para ti ✨",
            "youtube_link" => "",
            "title" => "",
            "created_at" => null
        );
    }
}

// Función para obtener ID de YouTube
function getYouTubeId($url) {
    if ($url == "") return "";
    
    if (preg_match('/youtu\.be\/([^\?]+)/', $url, $match)) {
        return $match[1];
    }
    if (preg_match('/v=([^&]+)/', $url, $match)) {
        return $match[1];
    }
    if (preg_match('/embed\/([^\?]+)/', $url, $match)) {
        return $match[1];
    }
    if (preg_match('/youtube\.com\/v\/([^\?]+)/', $url, $match)) {
        return $match[1];
    }
    return "";
}

$video_id = getYouTubeId($data['youtube_link']);
$hasVideo = ($video_id != "");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="Un mensaje especial con música para ti">
    <link href="css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
</head>
<body class="index-page">
    
    <!-- Botón Admin flotante -->
    <a href="login.php" class="admin-fab" aria-label="Panel de administración">
        <div class="fab-icon">⚙️</div>
        <span class="fab-text">Admin</span>
    </a>

    <main class="container">
        <div class="dedication-card">
            <!-- Elementos decorativos -->
            <div class="bg-blur bg-blur-1"></div>
            <div class="bg-blur bg-blur-2"></div>
            
            <!-- Header con animación -->
            <div class="card-header">
                <div class="heart-float">💖</div>
                <div class="sparkle sparkle-1">✨</div>
                <div class="sparkle sparkle-2">✨</div>
                <h1 class="gradient-text">Mensaje Especial</h1>
                <p class="subtitle">Alguien quiere decirte algo...</p>
            </div>
            
            <!-- Mensaje principal -->
            <div class="message-wrapper">
                <div class="message-bubble">
                    <div class="bubble-tail"></div>
                    <p class="message-text">
                        <?php echo nl2br(htmlspecialchars(trim($data['message']))); ?>
                    </p>
                </div>
            </div>
            
            <!-- Información de canción -->
            <?php if ($data['title'] != ""): ?>
            <div class="song-badge">
                <span class="music-icon">🎵</span>
                <span class="song-name"><?php echo htmlspecialchars($data['title']); ?></span>
            </div>
            <?php endif; ?>
            
            <!-- Reproductor de YouTube -->
            <?php if ($hasVideo): ?>
            <div class="video-section">
                <div class="video-player">
                    <iframe 
                        src="https://www.youtube.com/embed/<?php echo htmlspecialchars($video_id); ?>?autoplay=0&rel=0&modestbranding=1&color=white"
                        title="Reproductor de música"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy">
                    </iframe>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Footer -->
            <div class="card-footer">
                <?php if ($data['created_at'] != null): ?>
                <div class="date-badge">
                    <span>📅</span>
                    <time datetime="<?php echo $data['created_at']; ?>">
                        <?php echo date('d/m/Y', strtotime($data['created_at'])); ?>
                    </time>
                </div>
                <?php endif; ?>
                
                <a href="history.php" class="history-btn">
                    <span>Ver todas las dedicatorias</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var card = document.querySelector('.dedication-card');
            if (card) {
                card.style.animation = 'fadeInUp 0.8s cubic-bezier(0.2, 0.9, 0.4, 1.1) forwards';
            }
        });
    </script>
</body>
</html>