<?php
require_once 'config.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del POST
    $totalCoins = isset($_POST['totalCoins']) ? floatval($_POST['totalCoins']) : 0;
    $totalClicks = isset($_POST['totalClicks']) ? intval($_POST['totalClicks']) : 0;
    
    try {
        // Actualizar progreso del jugador (ID 1)
        $stmt = $pdo->prepare("UPDATE player_progress SET total_coins = ?, total_clicks = ? WHERE id = 1");
        $success = $stmt->execute([$totalCoins, $totalClicks]);
        
        if ($success) {
            echo json_encode(['status' => 'success', 'message' => 'Progreso guardado']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar progreso']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error de base de datos: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>