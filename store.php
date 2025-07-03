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
        <div class="row justify-content-center mt-4">
            <div class="col-12 text-center">
                <h1 class="text-white mb-4">🛍️ Tienda del Clicker</h1>
                <a href="index.php" class="nav-button">
                    <i class="fas fa-gamepad"></i> Volver al Juego
                </a>
            </div>
        </div>

        <!-- Player Stats -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="player-stats">
                    <div class="row">
                        <div class="col-md-3">
                            <h4><?php echo formatNumber($player['total_coins']); ?></h4>
                            <p>💰 Monedas</p>
                        </div>
                        <div class="col-md-3">
                            <h4><?php echo formatNumber($player['coins_per_click']); ?></h4>
                            <p>🖱️ Por Click</p>
                        </div>
                        <div class="col-md-3">
                            <h4><?php echo formatNumber($player['coins_per_second']); ?></h4>
                            <p>⏱️ Por Segundo</p>
                        </div>
                        <div class="col-md-3">
                            <h4><?php echo number_format($player['total_clicks']); ?></h4>
                            <p>👆 Clicks Totales</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <?php if ($message): ?>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="alert alert-custom alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
                        <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Store Items -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="store-container">
                    <h3 class="text-center mb-4">🛒 Items Disponibles</h3>
                    
                    <?php if (empty($storeItems)): ?>
                        <div class="text-center">
                            <p>No hay items disponibles en la tienda.</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($storeItems as $item): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="item-card text-center">
                                        <div class="item-icon">
                                            <?php echo $item['image_url'] ?: '📦'; ?>
                                        </div>
                                        <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                                        <p class="text-muted"><?php echo htmlspecialchars($item['description']); ?></p>
                                        
                                        <div class="item-price mb-2">
                                            💰 <?php echo formatNumber($item['price']); ?>
                                        </div>
                                        
                                        <div class="item-benefit mb-3">
                                            <?php if ($item['benefit_per_click'] > 0): ?>
                                                <div>🖱️ +<?php echo formatNumber($item['benefit_per_click']); ?> por click</div>
                                            <?php endif; ?>
                                            <?php if ($item['benefit_per_second'] > 0): ?>
                                                <div>⏱️ +<?php echo formatNumber($item['benefit_per_second']); ?> por segundo</div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="buy_item">
                                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="buy-button" 
                                                <?php echo $player['total_coins'] < $item['price'] ? 'disabled' : ''; ?>>
                                                <i class="fas fa-shopping-cart"></i> Comprar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Player Items -->
        <?php if (!empty($playerItems)): ?>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="store-container">
                        <h3 class="text-center mb-4">📦 Items Comprados</h3>
                        <div class="owned-items">
                            <?php foreach ($playerItems as $item): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span class="mr-2"><?php echo $item['image_url'] ?: '📦'; ?></span>
                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                        <span class="text-muted">(x<?php echo $item['quantity']; ?>)</span>
                                    </div>
                                    <div class="text-right">
                                        <?php if ($item['benefit_per_click'] > 0): ?>
                                            <small class="text-success">🖱️ +<?php echo formatNumber($item['benefit_per_click'] * $item['quantity']); ?></small><br>
                                        <?php endif; ?>
                                        <?php if ($item['benefit_per_second'] > 0): ?>
                                            <small class="text-success">⏱️ +<?php echo formatNumber($item['benefit_per_second'] * $item['quantity']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Admin Section -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="admin-section">
                    <h4 class="text-center mb-4">⚙️ Panel de Administración</h4>
                    
                    <!-- Add Item Form -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>📝 Agregar Nuevo Item</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="add_item">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nombre del Item</label>
                                            <input type="text" name="item_name" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Precio</label>
                                            <input type="number" step="0.01" name="item_price" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <textarea name="item_description" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Beneficio por Click</label>
                                            <input type="number" step="0.01" name="benefit_click" class="form-control" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Beneficio por Segundo</label>
                                            <input type="number" step="0.01" name="benefit_second" class="form-control" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Emoji/Icono</label>
                                            <input type="text" name="image_url" class="form-control" placeholder="🎮">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Agregar Item
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Manage Items -->
                    <div class="card">
                        <div class="card-header">
                            <h5>📋 Gestionar Items</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
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
                                                    <span class="mr-2"><?php echo $item['image_url'] ?: '📦'; ?></span>
                                                    <?php echo htmlspecialchars($item['name']); ?>
                                                </td>
                                                <td><?php echo formatNumber($item['price']); ?></td>
                                                <td><?php echo formatNumber($item['benefit_per_click']); ?></td>
                                                <td><?php echo formatNumber($item['benefit_per_second']); ?></td>
                                                <td>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="delete_item">
                                                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                                onclick="return confirm('¿Estás seguro de eliminar este item?')">
                                                            <i class="fas fa-trash"></i> Eliminar
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>