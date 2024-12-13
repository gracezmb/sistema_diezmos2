<?php
// mantenimiento.php
session_start();
require_once 'config/database.php';

// Función para obtener registros activos e inactivos de cualquier catálogo
function obtenerRegistros($pdo, $tabla) {
    $stmt = $pdo->prepare("SELECT * FROM $tabla ORDER BY nombre");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener los registros de cada catálogo
$tipos_moneda = obtenerRegistros($pdo, 'tipos_moneda');
$tipos_ofrenda = obtenerRegistros($pdo, 'tipos_ofrenda');
$bancos = obtenerRegistros($pdo, 'bancos');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento - Sistema de Diezmos y Ofrendas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/mantenimiento.css">
</head>

<body>
    <div class="container">
        <nav class="menu">
            <ul>
                <li><a href="index.php">Nuevo Sobre</a></li>
                <li><a href="reportes.php">Reportes</a></li>
                <li><a href="mantenimiento.php">Mantenimiento</a></li>
            </ul>
        </nav>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="maintenance-tabs">
            <div class="tab-buttons">
                <button class="tab-btn active" data-tab="monedas">Tipos de Moneda</button>
                <button class="tab-btn" data-tab="ofrendas">Tipos de Ofrenda</button>
                <button class="tab-btn" data-tab="bancos">Bancos</button>
            </div>

            <!-- Tipos de Moneda -->
            <div class="tab-content active" id="monedas">
                <h2>Tipos de Moneda</h2>
                <button class="btn-add" onclick="mostrarModal('moneda')">Agregar Moneda</button>
                
                <table class="maintenance-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Símbolo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tipos_moneda as $moneda): ?>
                            <tr data-id="<?php echo $moneda['id']; ?>">
                                <td><?php echo htmlspecialchars($moneda['codigo']); ?></td>
                                <td><?php echo htmlspecialchars($moneda['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($moneda['simbolo']); ?></td>
                                <td>
                                    <span class="estado-<?php echo $moneda['activo'] ? 'activo' : 'inactivo'; ?>">
                                        <?php echo $moneda['activo'] ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-edit" onclick="editarRegistro('moneda', <?php echo $moneda['id']; ?>)">
                                        Editar
                                    </button>
                                    <button class="btn-toggle" onclick="toggleEstado('moneda', <?php echo $moneda['id']; ?>)">
                                        <?php echo $moneda['activo'] ? 'Desactivar' : 'Activar'; ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tipos de Ofrenda -->
            <div class="tab-content" id="ofrendas">
                <h2>Tipos de Ofrenda</h2>
                <button class="btn-add" onclick="mostrarModal('ofrenda')">Agregar Tipo de Ofrenda</button>
                
                <table class="maintenance-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tipos_ofrenda as $ofrenda): ?>
                            <tr data-id="<?php echo $ofrenda['id']; ?>">
                                <td><?php echo htmlspecialchars($ofrenda['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($ofrenda['descripcion']); ?></td>
                                <td>
                                    <span class="estado-<?php echo $ofrenda['activo'] ? 'activo' : 'inactivo'; ?>">
                                        <?php echo $ofrenda['activo'] ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-edit" onclick="editarRegistro('ofrenda', <?php echo $ofrenda['id']; ?>)">
                                        Editar
                                    </button>
                                    <button class="btn-toggle" onclick="toggleEstado('ofrenda', <?php echo $ofrenda['id']; ?>)">
                                        <?php echo $ofrenda['activo'] ? 'Desactivar' : 'Activar'; ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Bancos -->
            <div class="tab-content" id="bancos">
                <h2>Bancos</h2>
                <button class="btn-add" onclick="mostrarModal('banco')">Agregar Banco</button>
                
                <table class="maintenance-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Código</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bancos as $banco): ?>
                            <tr data-id="<?php echo $banco['id']; ?>">
                                <td><?php echo htmlspecialchars($banco['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($banco['codigo']); ?></td>
                                <td>
                                    <span class="estado-<?php echo $banco['activo'] ? 'activo' : 'inactivo'; ?>">
                                        <?php echo $banco['activo'] ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-edit" onclick="editarRegistro('banco', <?php echo $banco['id']; ?>)">
                                        Editar
                                    </button>
                                    <button class="btn-toggle" onclick="toggleEstado('banco', <?php echo $banco['id']; ?>)">
                                        <?php echo $banco['activo'] ? 'Desactivar' : 'Activar'; ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modales para agregar/editar registros -->
    <?php include 'mantenimiento_modales.php'; ?>
    
    <script src="assets/js/mantenimiento.js"></script>
</body>
</html>