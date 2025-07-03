<?php
require_once 'config.php';

// Obtener datos del jugador
$player = getPlayerProgress($pdo);
if (!$player) {
    // Si no existe el jugador, crear uno nuevo
    $pdo->exec("INSERT INTO player_progress (player_name) VALUES ('Jugador')");
    $player = getPlayerProgress($pdo);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🍪 Clicker Game - Juego Principal</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
        }
        
        .game-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            margin: 2rem 0;
        }
        
        .click-button {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: none;
            font-size: 4rem;
            background: linear-gradient(45deg, #ff6b6b, #ffa726);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .click-button:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        }
        
        .click-button:active {
            transform: scale(0.95);
        }
        
        .stats-card {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .stats-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .nav-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .nav-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            color: white;
            text-decoration: none;
        }
        
        .floating-coin {
            position: absolute;
            font-size: 1.5rem;
            color: #ffd700;
            font-weight: bold;
            pointer-events: none;
            animation: floatUp 1s ease-out forwards;
        }
        
        @keyframes floatUp {
            0% {
                opacity: 1;
                transform: translateY(0);
            }
            100% {
                opacity: 0;
                transform: translateY(-50px);
            }
        }
        
        .progress-bar {
            background: linear-gradient(90deg, #ff6b6b, #ffa726);
            height: 8px;
            border-radius: 4px;
        }
        
        .auto-clicker {
            font-size: 0.8rem;
            color: #28a745;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="row justify-content-center mt-4">
            <div class="col-12 text-center">
                <h1 class="text-white mb-4">🍪 Cookie Clicker Game</h1>
                <a href="store.php" class="nav-button">
                    <i class="fas fa-store"></i> Ir a la Tienda
                </a>
            </div>
        </div>

        <!-- Main Game Area -->
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="game-container text-center">
                    <h3 class="mb-4">¡Haz Click para Ganar Monedas!</h3>
                    
                    <!-- Click Button -->
                    <div class="mb-4 position-relative" id="clickArea">
                        <button id="clickButton" class="click-button">
                            🍪
                        </button>
                    </div>
                    
                    <!-- Stats -->
                    <div class="stats-card">
                        <div class="stats-value" id="totalCoins">
                            <?php echo formatNumber($player['total_coins']); ?>
                        </div>
                        <div class="stats-label">💰 Monedas Totales</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-value" id="coinsPerClick">
                                    <?php echo formatNumber($player['coins_per_click']); ?>
                                </div>
                                <div class="stats-label">🖱️ Por Click</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-value" id="coinsPerSecond">
                                    <?php echo formatNumber($player['coins_per_second']); ?>
                                </div>
                                <div class="stats-label">⏱️ Por Segundo</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="stats-card">
                                <div class="stats-value" id="totalClicks">
                                    <?php echo number_format($player['total_clicks']); ?>
                                </div>
                                <div class="stats-label">👆 Clicks Totales</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Auto-clicker info -->
                    <div class="auto-clicker" id="autoClickerInfo">
                        <?php if ($player['coins_per_second'] > 0): ?>
                            <i class="fas fa-cog fa-spin"></i> 
                            Generando automáticamente: <?php echo formatNumber($player['coins_per_second']); ?> monedas/segundo
                        <?php else: ?>
                            <i class="fas fa-info-circle"></i> 
                            ¡Compra items en la tienda para generar monedas automáticamente!
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Game Info -->
        <div class="row justify-content-center mt-4">
            <div class="col-lg-8">
                <div class="game-container">
                    <h4 class="text-center mb-3">📊 Estadísticas del Juego</h4>
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h5>🎯 Objetivo</h5>
                            <p>Hacer click en la galleta para ganar monedas y comprar mejoras</p>
                        </div>
                        <div class="col-md-3">
                            <h5>🛍️ Tienda</h5>
                            <p>Compra items que te den más monedas por click o automáticamente</p>
                        </div>
                        <div class="col-md-3">
                            <h5>💾 Progreso</h5>
                            <p>Tu progreso se guarda automáticamente cada vez que haces click</p>
                        </div>
                        <div class="col-md-3">
                            <h5>🚀 Estrategia</h5>
                            <p>Equilibra entre mejoras de click y generación automática</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        // Variables del juego
        let playerData = {
            totalCoins: <?php echo $player['total_coins']; ?>,
            coinsPerClick: <?php echo $player['coins_per_click']; ?>,
            coinsPerSecond: <?php echo $player['coins_per_second']; ?>,
            totalClicks: <?php echo $player['total_clicks']; ?>
        };
        
        let saveTimeout;
        
        // Función para formatear números grandes
        function formatNumber(num) {
            if (num >= 1000000000) {
                return (num / 1000000000).toFixed(1) + 'B';
            } else if (num >= 1000000) {
                return (num / 1000000).toFixed(1) + 'M';
            } else if (num >= 1000) {
                return (num / 1000).toFixed(1) + 'K';
            } else {
                return Math.floor(num).toLocaleString();
            }
        }
        
        // Actualizar UI
        function updateUI() {
            $('#totalCoins').text(formatNumber(playerData.totalCoins));
            $('#coinsPerClick').text(formatNumber(playerData.coinsPerClick));
            $('#coinsPerSecond').text(formatNumber(playerData.coinsPerSecond));
            $('#totalClicks').text(playerData.totalClicks.toLocaleString());
        }
        
        // Mostrar monedas flotantes
        function showFloatingCoins(x, y, amount) {
            const floatingCoin = $('<div class="floating-coin">+' + formatNumber(amount) + '</div>');
            floatingCoin.css({
                left: x - 25,
                top: y - 25
            });
            
            $('#clickArea').append(floatingCoin);
            
            setTimeout(() => {
                floatingCoin.remove();
            }, 1000);
        }
        
        // Guardar progreso
        function saveProgress() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                $.ajax({
                    url: 'save_progress.php',
                    method: 'POST',
                    data: {
                        totalCoins: playerData.totalCoins,
                        totalClicks: playerData.totalClicks
                    },
                    success: function(response) {
                        console.log('Progreso guardado automáticamente');
                    },
                    error: function() {
                        console.log('Error al guardar progreso');
                    }
                });
            }, 1000);
        }
        
        // Evento de click
        $('#clickButton').click(function(e) {
            const rect = e.target.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            // Agregar monedas por click
            playerData.totalCoins += playerData.coinsPerClick;
            playerData.totalClicks++;
            
            // Mostrar efecto visual
            showFloatingCoins(e.clientX, e.clientY, playerData.coinsPerClick);
            
            // Actualizar UI
            updateUI();
            
            // Guardar progreso
            saveProgress();
            
            // Efecto de vibración en móvil
            if (navigator.vibrate) {
                navigator.vibrate(50);
            }
        });
        
        // Generación automática (cada segundo)
        setInterval(function() {
            if (playerData.coinsPerSecond > 0) {
                playerData.totalCoins += playerData.coinsPerSecond;
                updateUI();
                saveProgress();
            }
        }, 1000);
        
        // Inicializar UI
        updateUI();
    </script>
</body>
</html>
