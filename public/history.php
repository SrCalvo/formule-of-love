<?php
require_once "../config/db.php";

// Consulta para obtener todas las dedicatorias
$stmt = $conn->prepare("
    SELECT d.id, d.message, s.youtube_link, s.title, d.created_at, d.is_active
    FROM dedications d
    JOIN songs s ON d.song_id = s.id
    ORDER BY d.created_at DESC
");

$stmt->execute();
$history = $stmt->get_result();
$hasResults = $history->num_rows > 0;

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
    return "";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Dedicatorias 💖</title>
    <link href="css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        /* Estilos adicionales específicos para el historial */
        .history-header-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(99, 102, 241, 0.2);
        }
        
        .filter-badge {
            background: linear-gradient(135deg, #6366f1, #ec489a);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 100px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .history-grid {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        
        /* Estilo de tarjeta de historial similar al index */
        .history-dedication-card {
            background: white;
            border-radius: 32px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.1);
        }
        
        .history-dedication-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .active-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 10;
        }
        
        .inactive-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #94a3b8;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 10;
        }
        
        .history-date-header {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #f1f5f9;
            padding: 0.5rem 1rem;
            border-radius: 100px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            color: #475569;
        }
        
        .history-message-preview {
            background: linear-gradient(135deg, #6366f1 0%, #ec489a 100%);
            padding: 1.5rem;
            border-radius: 24px;
            margin: 1.5rem 0;
            position: relative;
        }
        
        .history-message-preview::before {
            content: '"';
            position: absolute;
            top: 10px;
            left: 20px;
            font-size: 4rem;
            opacity: 0.2;
            font-family: serif;
            color: white;
        }
        
        .history-message-text {
            color: white;
            font-size: 1rem;
            line-height: 1.7;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        
        .history-song-info {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(99, 102, 241, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 100px;
            margin: 1rem 0;
        }
        
        .history-video-preview {
            margin: 1.5rem 0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .history-video-preview iframe {
            width: 100%;
            height: 200px;
            border: none;
        }
        
        .view-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #6366f1;
            color: white;
            padding: 0.625rem 1.25rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }
        
        .view-link:hover {
            background: #4f46e5;
            transform: translateX(4px);
            gap: 0.75rem;
        }
        
        @media (max-width: 768px) {
            .history-dedication-card {
                padding: 1.25rem;
            }
            
            .history-message-preview {
                padding: 1rem;
            }
            
            .history-video-preview iframe {
                height: 180px;
            }
        }
    </style>
</head>
<body class="history-page">
    <div class="history-container">
        <div class="history-main-card">
            <div class="history-header-controls">
                <div class="history-header">
                    <h2>📜 Historial de Dedicatorias</h2>
                    <p>Todos los mensajes especiales que han sido enviados</p>
                </div>
                <div class="filter-badge">
                    📊 Total: <?php echo $history->num_rows; ?> dedicatorias
                </div>
            </div>

            <?php if ($hasResults): ?>
            <div class="history-grid">
                <?php 
                // Reiniciar el puntero del resultado para recorrerlo
                $history->data_seek(0);
                while($row = $history->fetch_assoc()): 
                    $video_id = getYouTubeId($row['youtube_link']);
                    $hasVideo = ($video_id != "");
                    $isActive = ($row['is_active'] == 1);
                ?>
                <div class="history-dedication-card">
                    <!-- Badge de estado -->
                    <?php if ($isActive): ?>
                    <div class="active-badge">
                        ✨ ACTIVA ACTUALMENTE
                    </div>
                    <?php else: ?>
                    <div class="inactive-badge">
                        📅 DEDICATORIA ANTERIOR
                    </div>
                    <?php endif; ?>
                    
                    <!-- Fecha -->
                    <div class="history-date-header">
                        <span>📅</span>
                        <span><?php echo date('d/m/Y - H:i:s', strtotime($row['created_at'])); ?></span>
                    </div>
                    
                    <!-- Título de canción (si existe) -->
                    <?php if ($row['title'] != ""): ?>
                    <div class="history-song-info">
                        <span>🎵</span>
                        <span><strong><?php echo htmlspecialchars($row['title']); ?></strong></span>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Mensaje con estilo similar al index -->
                    <div class="history-message-preview">
                        <div class="history-message-text">
                            <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                        </div>
                    </div>
                    
                    <!-- Video de YouTube (vista previa) -->
                    <?php if ($hasVideo): ?>
                    <div class="history-video-preview">
                        <iframe 
                            src="https://www.youtube.com/embed/<?php echo $video_id; ?>?autoplay=0&rel=0&modestbranding=1"
                            title="Vista previa de canción"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            loading="lazy">
                        </iframe>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Enlace para ver en YouTube (si hay video) -->
                    <?php if ($hasVideo): ?>
                    <a href="<?php echo htmlspecialchars($row['youtube_link']); ?>" 
                       class="view-link" 
                       target="_blank" 
                       rel="noopener noreferrer">
                        <span>🎬</span>
                        <span>Ver en YouTube</span>
                        <span>→</span>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="empty-history">
                <span>💔</span>
                <p>Todavía no hay dedicatorias</p>
                <p style="font-size: 0.875rem; margin-top: 0.5rem;">¡Anímate a crear la primera desde el panel de administración!</p>
                <a href="dashboard.php" style="display: inline-block; margin-top: 1.5rem; background: #6366f1; color: white; padding: 0.75rem 1.5rem; border-radius: 12px; text-decoration: none;">
                    Ir al panel de administración
                </a>
            </div>
            <?php endif; ?>

            <div class="back-to-home">
                <a href="index.php">
                    <span>←</span>
                    <span>Volver al inicio</span>
                </a>
                <?php if ($hasResults): ?>
                <a href="dashboard.php" style="margin-left: 1rem; background: #6366f1; color: white; padding: 0.75rem 1.5rem; border-radius: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                    <span>⚙️</span>
                    <span>Panel Admin</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Animación al hacer scroll
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.history-dedication-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>