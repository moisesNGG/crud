-- =============================================
-- DATABASE SETUP FOR CLICKER GAME
-- =============================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS clicker_game_db;
USE clicker_game_db;

-- =============================================
-- TABLA: store_items (Items de la tienda)
-- =============================================
CREATE TABLE store_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(15,2) NOT NULL,
    benefit_per_click DECIMAL(10,2) DEFAULT 0.00,
    benefit_per_second DECIMAL(10,2) DEFAULT 0.00,
    image_url VARCHAR(255) DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- TABLA: player_progress (Progreso del jugador)
-- =============================================
CREATE TABLE player_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_name VARCHAR(50) NOT NULL DEFAULT 'Jugador',
    total_coins DECIMAL(15,2) DEFAULT 0.00,
    coins_per_click DECIMAL(10,2) DEFAULT 1.00,
    coins_per_second DECIMAL(10,2) DEFAULT 0.00,
    total_clicks INT DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- TABLA: player_items (Items comprados por el jugador)
-- =============================================
CREATE TABLE player_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT DEFAULT 1,
    purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES player_progress(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES store_items(id) ON DELETE CASCADE
);

-- =============================================
-- DATOS INICIALES
-- =============================================

-- Insertar progreso inicial del jugador
INSERT INTO player_progress (player_name, total_coins, coins_per_click, coins_per_second, total_clicks) 
VALUES ('Jugador', 0.00, 1.00, 0.00, 0);

-- Insertar items iniciales de la tienda
INSERT INTO store_items (name, description, price, benefit_per_click, benefit_per_second, image_url) VALUES
('Cursor Mejorado', 'Duplica las monedas por click', 15.00, 1.00, 0.00, '🖱️'),
('Abuela', 'Genera 1 moneda por segundo automáticamente', 100.00, 0.00, 1.00, '👵'),
('Granja', 'Genera 8 monedas por segundo', 1100.00, 0.00, 8.00, '🚜'),
('Mina', 'Genera 47 monedas por segundo', 12000.00, 0.00, 47.00, '⛏️'),
('Fábrica', 'Genera 260 monedas por segundo', 130000.00, 0.00, 260.00, '🏭'),
('Banco', 'Genera 1400 monedas por segundo', 1400000.00, 0.00, 1400.00, '🏦'),
('Templo', 'Genera 7800 monedas por segundo', 20000000.00, 0.00, 7800.00, '🏛️'),
('Torre Mágica', 'Genera 44000 monedas por segundo', 330000000.00, 0.00, 44000.00, '🗼'),
('Nave Espacial', 'Genera 260000 monedas por segundo', 5100000000.00, 0.00, 260000.00, '🚀'),
('Portal Dimensional', 'Genera 1600000 monedas por segundo', 75000000000.00, 0.00, 1600000.00, '🌀');

-- =============================================
-- ÍNDICES para optimizar consultas
-- =============================================
CREATE INDEX idx_player_items_player_id ON player_items(player_id);
CREATE INDEX idx_player_items_item_id ON player_items(item_id);
CREATE INDEX idx_store_items_active ON store_items(is_active);

-- =============================================
-- VISTA para facilitar consultas
-- =============================================
CREATE VIEW player_store_summary AS
SELECT 
    p.id as player_id,
    p.player_name,
    p.total_coins,
    p.coins_per_click,
    p.coins_per_second,
    p.total_clicks,
    COUNT(pi.id) as total_items_owned
FROM player_progress p
LEFT JOIN player_items pi ON p.id = pi.player_id
GROUP BY p.id;

-- =============================================
-- PROCEDIMIENTOS ALMACENADOS
-- =============================================

-- Procedimiento para comprar un item
DELIMITER //
CREATE PROCEDURE BuyItem(
    IN p_player_id INT,
    IN p_item_id INT,
    IN p_quantity INT
)
BEGIN
    DECLARE item_price DECIMAL(15,2);
    DECLARE player_coins DECIMAL(15,2);
    DECLARE total_cost DECIMAL(15,2);
    DECLARE benefit_click DECIMAL(10,2);
    DECLARE benefit_second DECIMAL(10,2);
    
    -- Obtener precio del item y beneficios
    SELECT price, benefit_per_click, benefit_per_second 
    INTO item_price, benefit_click, benefit_second
    FROM store_items 
    WHERE id = p_item_id AND is_active = TRUE;
    
    -- Calcular costo total
    SET total_cost = item_price * p_quantity;
    
    -- Obtener monedas del jugador
    SELECT total_coins INTO player_coins 
    FROM player_progress 
    WHERE id = p_player_id;
    
    -- Verificar si tiene suficientes monedas
    IF player_coins >= total_cost THEN
        -- Actualizar monedas del jugador
        UPDATE player_progress 
        SET total_coins = total_coins - total_cost,
            coins_per_click = coins_per_click + (benefit_click * p_quantity),
            coins_per_second = coins_per_second + (benefit_second * p_quantity)
        WHERE id = p_player_id;
        
        -- Registrar compra
        INSERT INTO player_items (player_id, item_id, quantity) 
        VALUES (p_player_id, p_item_id, p_quantity);
        
        SELECT 'SUCCESS' as result, 'Item comprado exitosamente' as message;
    ELSE
        SELECT 'ERROR' as result, 'No tienes suficientes monedas' as message;
    END IF;
END//
DELIMITER ;

-- Mostrar estructura creada
SHOW TABLES;