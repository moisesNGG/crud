# 🍪 Clicker Game con Tienda

Un juego clicker completo con sistema de tienda desarrollado en PHP + MySQL. ¡Haz click para ganar monedas y compra mejoras para hacer crecer tu imperio!

## 🎮 Características

- **Juego Clicker**: Haz click en la galleta para ganar monedas
- **Tienda Completa**: Compra items que mejoran tu producción
- **Generación Automática**: Los items generan monedas por segundo
- **Sistema CRUD**: Administra items de la tienda
- **Guardado Automático**: Tu progreso se guarda automáticamente
- **Diseño Responsive**: Funciona en desktop y móvil

## 🚀 Instalación

### Requisitos
- XAMPP (Apache + MySQL + PHP)
- Navegador web moderno

### Pasos de Instalación

1. **Configurar XAMPP**
   - Instalar XAMPP
   - Iniciar Apache y MySQL

2. **Importar Base de Datos**
   - Abrir phpMyAdmin (http://localhost/phpmyadmin)
   - Crear nueva base de datos o importar el archivo `database_setup.sql`
   - Ejecutar el script SQL completo

3. **Configurar Archivos**
   - Copiar todos los archivos a la carpeta `htdocs` de XAMPP
   - Asegurarse de que `config.php` tiene las credenciales correctas de MySQL

4. **Ejecutar el Juego**
   - Abrir navegador
   - Ir a `http://localhost/[nombre_carpeta]/index.php`
   - ¡Comenzar a jugar!

## 🎯 Cómo Jugar

### Página Principal (index.php)
- Haz click en la galleta 🍪 para ganar monedas
- Observa tus estadísticas en tiempo real
- El juego guarda automáticamente tu progreso

### Tienda (store.php)
- Compra items para mejorar tu producción
- Algunos items dan más monedas por click
- Otros items generan monedas automáticamente cada segundo
- Administra la tienda agregando/eliminando items

## 📊 Estructura de Base de Datos

### Tablas Principales
- `player_progress`: Progreso del jugador
- `store_items`: Items disponibles en la tienda
- `player_items`: Items comprados por el jugador

### Procedimientos Almacenados
- `BuyItem`: Maneja la compra de items de forma segura

## 🛠️ Archivos Principales

- `index.php`: Página principal del juego
- `store.php`: Tienda y panel de administración
- `config.php`: Configuración y funciones de base de datos
- `save_progress.php`: Guardado automático del progreso
- `database_setup.sql`: Script de instalación de la base de datos

## 🎨 Personalización

### Agregar Nuevos Items
1. Ir a la tienda
2. Usar el panel de administración
3. Llenar el formulario con:
   - Nombre del item
   - Precio
   - Beneficio por click
   - Beneficio por segundo
   - Emoji/icono

### Modificar Estilos
- Los estilos están incluidos en cada archivo PHP
- Usar CSS personalizado para cambiar colores y efectos

## 🔧 Configuración Avanzada

### Credenciales de Base de Datos
Editar `config.php`:
```php
$servidor = "mysql:dbname=clicker_game_db;host=127.0.0.1";
$usuario = "root";
$password = "";
```

### Items Iniciales
Los items se crean automáticamente con el script SQL. Incluye:
- Cursor Mejorado
- Abuela
- Granja
- Mina
- Fábrica
- Banco
- Templo
- Torre Mágica
- Nave Espacial
- Portal Dimensional

## 🎪 Funcionalidades Técnicas

- **AJAX**: Para guardado automático sin recargar página
- **Procedimientos Almacenados**: Para transacciones seguras
- **Responsive Design**: Bootstrap 4 con estilos personalizados
- **Validaciones**: Tanto frontend como backend
- **Formato de Números**: Convierte números grandes a K, M, B

## 🐛 Solución de Problemas

### Error de Conexión a BD
- Verificar que MySQL esté ejecutándose
- Verificar credenciales en `config.php`
- Verificar que la base de datos existe

### El juego no guarda progreso
- Verificar permisos de archivos
- Revisar console del navegador para errores JavaScript
- Verificar que `save_progress.php` esté accesible

## 🚀 Mejoras Futuras

- Sistema de logros
- Múltiples jugadores
- Eventos especiales
- Más tipos de items
- Gráficos mejorados
- Sistema de prestige

## 📱 Compatibilidad

- Chrome, Firefox, Safari, Edge
- Funciona en dispositivos móviles
- Optimizado para pantallas táctiles

---

**¡Disfruta del juego! 🎮**