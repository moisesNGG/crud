<?php
require_once 'config.php';

// Procesar acciones del formulario
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'buy_item':
            $itemId = intval($_POST['item_id'] ?? 0);
            $quantity = intval($_POST['quantity'] ?? 1);
            
            if ($itemId > 0 && $quantity > 0) {
                try {
                    // Llamar al procedimiento almacenado para comprar item
                    $stmt = $pdo->prepare("CALL BuyItem(1, ?, ?)");
                    $stmt->execute([$itemId, $quantity]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // IMPORTANTE: Cerrar el cursor para evitar errores
                    $stmt->closeCursor();
                    
                    if ($result['result'] === 'SUCCESS') {
                        $message = $result['message'];
                        $messageType = 'success';
                    } else {
                        $message = $result['message'];
                        $messageType = 'error';
                    }
                } catch (PDOException $e) {
                    $message = 'Error al comprar item: ' . $e->getMessage();
                    $messageType = 'error';
                }
            }
            break;
            
        case 'add_item':
            $name = $_POST['item_name'] ?? '';
            $description = $_POST['item_description'] ?? '';
            $price = floatval($_POST['item_price'] ?? 0);
            $benefitClick = floatval($_POST['benefit_click'] ?? 0);
            $benefitSecond = floatval($_POST['benefit_second'] ?? 0);
            $imageUrl = $_POST['image_url'] ?? '';
            
            if ($name && $price > 0) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO store_items (name, description, price, benefit_per_click, benefit_per_second, image_url) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $description, $price, $benefitClick, $benefitSecond, $imageUrl]);
                    $message = 'Item agregado exitosamente';
                    $messageType = 'success';
                } catch (PDOException $e) {
                    $message = 'Error al agregar item: ' . $e->getMessage();
                    $messageType = 'error';
                }
            }
            break;
            
        case 'delete_item':
            $itemId = intval($_POST['item_id'] ?? 0);
            if ($itemId > 0) {
                try {
                    $stmt = $pdo->prepare("UPDATE store_items SET is_active = FALSE WHERE id = ?");
                    $stmt->execute([$itemId]);
                    $message = 'Item eliminado exitosamente';
                    $messageType = 'success';
                } catch (PDOException $e) {
                    $message = 'Error al eliminar item: ' . $e->getMessage();
                    $messageType = 'error';
                }
            }
            break;
    }
}

// Obtener datos
$player = getPlayerProgress($pdo);
$storeItems = getStoreItems($pdo);
$playerItems = getPlayerItems($pdo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛍️ Tienda - Clicker Game</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .header {
            background-color: #FF9800;
            color: white;
            padding: 20px;
            text-align: center;
            border: 2px solid #333;
            margin-bottom: 20px;
        }
        
        .nav-button {
            background-color: #2196F3;
            color: white;
            padding: 10px 20px;
            border: 2px solid #333;
            text-decoration: none;
            font-weight: bold;
        }
        
        .nav-button:hover {
            background-color: #1976D2;
            color: white;
            text-decoration: none;
        }
        
        .stats-box {
            background-color: white;
            border: 2px solid #333;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .stats-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .stat-item {
            flex: 1;
            min-width: 150px;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .store-box {
            background-color: white;
            border: 2px solid #333;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .item-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .item-card {
            background-color: #f9f9f9;
            border: 2px solid #ccc;
            padding: 15px;
            text-align: center;
        }
        
        .item-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .item-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .item-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .item-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #E91E63;
            margin-bottom: 10px;
        }
        
        .item-benefit {
            color: #4CAF50;
            font-weight: bold;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .buy-button {
            background-color: #4CAF50;
            color: white;
            border: 2px solid #333;
            padding: 8px 16px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .buy-button:hover {
            background-color: #45a049;
        }
        
        .buy-button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        
        .admin-box {
            background-color: #FFF9C4;
            border: 2px solid #333;
            padding: 20px;
            margin-top: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-control {
            width: 100%;
            padding: 8px;
            border: 2px solid #ccc;
            font-size: 14px;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .form-row .form-group {
            flex: 1;
            min-width: 150px;
        }
        
        .btn {
            background-color: #2196F3;
            color: white;
            border: 2px solid #333;
            padding: 10px 20px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .btn:hover {
            background-color: #1976D2;
        }
        
        .btn-success {
            background-color: #4CAF50;
        }
        
        .btn-success:hover {
            background-color: #45a049;
        }
        
        .btn-danger {
            background-color: #f44336;
        }
        
        .btn-danger:hover {
            background-color: #da190b;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 2px solid #333;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .owned-items {
            background-color: #f0f0f0;
            border: 2px solid #ccc;
            padding: 15px;
            margin-top: 20px;
        }
        
        .owned-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #ccc;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th, .table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        
        .table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        h1, h2, h3 {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🛍️ Tienda del Juego</h1>
            <a href="index.php" class="nav-button">VOLVER AL JUEGO</a>
        </div>

        <!-- Player Stats -->
        <div class="stats-box">
            <h3>📊 Tus Estadísticas</h3>
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-number"><?php echo formatNumber($player['total_coins']); ?></div>
                    <div class="stat-label">💰 Monedas</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo formatNumber($player['coins_per_click']); ?></div>
                    <div class="stat-label">🖱️ Por Click</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo formatNumber($player['coins_per_second']); ?></div>
                    <div class="stat-label">⏱️ Por Segundo</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo number_format($player['total_clicks']); ?></div>
                    <div class="stat-label">👆 Clicks</div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
                <strong><?php echo $messageType === 'success' ? '✅ Éxito:' : '❌ Error:'; ?></strong>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Store Items -->
        <div class="store-box">
            <h3>🛒 Items para Comprar</h3>
            
            <?php if (empty($storeItems)): ?>
                <p>No hay items disponibles.</p>
            <?php else: ?>
                <div class="item-grid">
                    <?php foreach ($storeItems as $item): ?>
                        <div class="item-card">
                            <div class="item-icon">
                                <?php echo $item['image_url'] ?: '📦'; ?>
                            </div>
                            <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                            <div class="item-description"><?php echo htmlspecialchars($item['description']); ?></div>
                            
                            <div class="item-price">
                                💰 <?php echo formatNumber($item['price']); ?>
                            </div>
                            
                            <div class="item-benefit">
                                <?php if ($item['benefit_per_click'] > 0): ?>
                                    🖱️ +<?php echo formatNumber($item['benefit_per_click']); ?> por click<br>
                                <?php endif; ?>
                                <?php if ($item['benefit_per_second'] > 0): ?>
                                    ⏱️ +<?php echo formatNumber($item['benefit_per_second']); ?> por segundo
                                <?php endif; ?>
                            </div>
                            
                            <form method="POST">
                                <input type="hidden" name="action" value="buy_item">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="buy-button" 
                                    <?php echo $player['total_coins'] < $item['price'] ? 'disabled' : ''; ?>>
                                    COMPRAR
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Player Items -->
        <?php if (!empty($playerItems)): ?>
            <div class="store-box">
                <h3>📦 Items que Tienes</h3>
                <div class="owned-items">
                    <?php foreach ($playerItems as $item): ?>
                        <div class="owned-item">
                            <div>
                                <span><?php echo $item['image_url'] ?: '📦'; ?></span>
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                <span>(Cantidad: <?php echo $item['quantity']; ?>)</span>
                            </div>
                            <div>
                                <?php if ($item['benefit_per_click'] > 0): ?>
                                    <small>🖱️ +<?php echo formatNumber($item['benefit_per_click'] * $item['quantity']); ?></small><br>
                                <?php endif; ?>
                                <?php if ($item['benefit_per_second'] > 0): ?>
                                    <small>⏱️ +<?php echo formatNumber($item['benefit_per_second'] * $item['quantity']); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Admin Section -->
        <div class="admin-box">
            <h3>⚙️ Administración de la Tienda</h3>
            
            <!-- Add Item Form -->
            <h4>📝 Agregar Nuevo Item</h4>
            <form method="POST">
                <input type="hidden" name="action" value="add_item">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre del Item</label>
                        <input type="text" name="item_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Precio</label>
                        <input type="number" step="0.01" name="item_price" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <input type="text" name="item_description" class="form-control">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Beneficio por Click</label>
                        <input type="number" step="0.01" name="benefit_click" class="form-control" value="0">
                    </div>
                    <div class="form-group">
                        <label>Beneficio por Segundo</label>
                        <input type="number" step="0.01" name="benefit_second" class="form-control" value="0">
                    </div>
                    <div class="form-group">
                        <label>Emoji/Icono</label>
                        <input type="text" name="image_url" class="form-control" placeholder="🎮">
                    </div>
                </div>
                <button type="submit" class="btn btn-success">AGREGAR ITEM</button>
            </form>

            <!-- Manage Items -->
            <h4 style="margin-top: 30px;">📋 Gestionar Items</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Beneficio Click</th>
                        <th>Beneficio Segundo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($storeItems as $item): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td>
                                <?php echo $item['image_url'] ?: '📦'; ?>
                                <?php echo htmlspecialchars($item['name']); ?>
                            </td>
                            <td><?php echo formatNumber($item['price']); ?></td>
                            <td><?php echo formatNumber($item['benefit_per_click']); ?></td>
                            <td><?php echo formatNumber($item['benefit_per_second']); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete_item">
                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="btn btn-danger" 
                                            onclick="return confirm('¿Seguro que quieres eliminar este item?')">
                                        ELIMINAR
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>