<?php
// =============================================
// CONFIGURACIÓN DE BASE DE DATOS
// =============================================

$servidor = "mysql:dbname=clicker_game_db;host=127.0.0.1";
$usuario = "root";  // Usuario por defecto de XAMPP
$password = "";     // Contraseña por defecto de XAMPP (vacía)

try {
    $pdo = new PDO($servidor, $usuario, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Comentar la línea siguiente en producción
    // echo "✅ BASE DE DATOS CONECTADA CORRECTAMENTE";
} catch (PDOException $e) {
    echo "❌ ERROR EN LA CONEXIÓN: " . $e->getMessage();
    die();
}

// =============================================
// FUNCIONES ÚTILES
// =============================================

/**
 * Obtener el progreso del jugador (siempre ID 1 para este juego simple)
 */
function getPlayerProgress($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM player_progress WHERE id = 1");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Actualizar progreso del jugador
 */
function updatePlayerProgress($pdo, $coins, $clicks = 0) {
    $stmt = $pdo->prepare("UPDATE player_progress SET total_coins = ?, total_clicks = total_clicks + ? WHERE id = 1");
    return $stmt->execute([$coins, $clicks]);
}

/**
 * Obtener items de la tienda
 */
function getStoreItems($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM store_items WHERE is_active = TRUE ORDER BY price ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtener items comprados por el jugador
 */
function getPlayerItems($pdo) {
    $stmt = $pdo->prepare("
        SELECT si.*, pi.quantity, pi.purchased_at 
        FROM player_items pi 
        JOIN store_items si ON pi.item_id = si.id 
        WHERE pi.player_id = 1 
        ORDER BY pi.purchased_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Formatear número grande
 */
function formatNumber($number) {
    if ($number >= 1000000000) {
        return round($number / 1000000000, 1) . 'B';
    } elseif ($number >= 1000000) {
        return round($number / 1000000, 1) . 'M';
    } elseif ($number >= 1000) {
        return round($number / 1000, 1) . 'K';
    } else {
        return number_format($number, 0);
    }
}
?>