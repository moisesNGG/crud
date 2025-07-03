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
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border: 2px solid #333;
            margin-bottom: 20px;
        }
        
        .game-box {
            background-color: white;
            border: 3px solid #333;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .click-button {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 3px solid #333;
            font-size: 3rem;
            background-color: #FFD700;
            cursor: pointer;
            margin: 20px auto;
        }
        
        .click-button:hover {
            background-color: #FFA500;
        }
        
        .stats-box {
            background-color: #E3F2FD;
            border: 2px solid #333;
            padding: 15px;
            margin: 10px 0;
            text-align: center;
        }
        
        .stats-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }
        
        .stats-label {
            font-size: 0.9rem;
            color: #666;
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
        
        .floating-coin {
            position: absolute;
            font-size: 1.2rem;
            color: #FFD700;
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
                transform: translateY(-30px);
            }
        }
        
        .info-box {
            background-color: #FFF3E0;
            border: 2px solid #333;
            padding: 15px;
            margin: 10px 0;
            font-size: 0.9rem;
            color: #333;
        }
        
        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .col {
            flex: 1;
            min-width: 200px;
        }
        
        h1 {
            margin: 0;
            font-size: 2rem;
        }
        
        h3 {
            margin: 10px 0;
            font-size: 1.3rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🍪 Juego del Click</h1>
            <a href="store.php" class="nav-button">IR A LA TIENDA</a>
        </div>

        <!-- Main Game -->
        <div class="game-box">
            <h3>¡Haz Click en la Galleta!</h3>
            
            <!-- Click Button -->
            <div id="clickArea" style="position: relative;">
                <button id="clickButton" class="click-button">🍪</button>
            </div>
            
            <!-- Stats -->
            <div class="row">
                <div class="col">
                    <div class="stats-box">
                        <div class="stats-number" id="totalCoins">
                            <?php echo formatNumber($player['total_coins']); ?>
                        </div>
                        <div class="stats-label">💰 Monedas</div>
                    </div>
                </div>
                <div class="col">
                    <div class="stats-box">
                        <div class="stats-number" id="coinsPerClick">
                            <?php echo formatNumber($player['coins_per_click']); ?>
                        </div>
                        <div class="stats-label">🖱️ Por Click</div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="stats-box">
                        <div class="stats-number" id="coinsPerSecond">
                            <?php echo formatNumber($player['coins_per_second']); ?>
                        </div>
                        <div class="stats-label">⏱️ Por Segundo</div>
                    </div>
                </div>
                <div class="col">
                    <div class="stats-box">
                        <div class="stats-number" id="totalClicks">
                            <?php echo number_format($player['total_clicks']); ?>
                        </div>
                        <div class="stats-label">👆 Clicks</div>
                    </div>
                </div>
            </div>
            
            <!-- Info -->
            <div class="info-box">
                <?php if ($player['coins_per_second'] > 0): ?>
                    ⚡ Generando: <?php echo formatNumber($player['coins_per_second']); ?> monedas/segundo
                <?php else: ?>
                    💡 Tip: Compra items en la tienda para generar monedas automáticamente
                <?php endif; ?>
            </div>
        </div>

        <!-- Instructions -->
        <div class="game-box">
            <h3>📋 Instrucciones</h3>
            <p><strong>1.</strong> Haz click en la galleta para ganar monedas</p>
            <p><strong>2.</strong> Ve a la tienda para comprar mejoras</p>
            <p><strong>3.</strong> Las mejoras te dan más monedas por click o automáticamente</p>
            <p><strong>4.</strong> Tu progreso se guarda automáticamente</p>
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
