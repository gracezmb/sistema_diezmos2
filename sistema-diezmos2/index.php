<?php
session_start();
require_once 'config/database.php';

// Obtener catálogos para el formulario
function obtenerCatalogos($pdo) {
    $catalogos = [];
    
    // Obtener tipos de ofrenda activos
    $stmt = $pdo->query("SELECT id, nombre FROM tipos_ofrenda WHERE activo = true ORDER BY nombre");
    $catalogos['tipos_ofrenda'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener tipos de moneda activos
    $stmt = $pdo->query("SELECT id, codigo, nombre, simbolo FROM tipos_moneda WHERE activo = true ORDER BY codigo");
    $catalogos['tipos_moneda'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener bancos activos
    $stmt = $pdo->query("SELECT id, nombre FROM bancos WHERE activo = true ORDER BY nombre");
    $catalogos['bancos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $catalogos;
}

// Obtener siguiente número de sobre
function getNextEnvelopeNumber($pdo) {
    $currentYear = date('Y');
    $currentMonth = date('m');
    
    $sql = "SELECT COALESCE(MAX(numero_sobre), 0) + 1 as next_number 
            FROM sobres 
            WHERE EXTRACT(YEAR FROM fecha) = :year 
            AND EXTRACT(MONTH FROM fecha) = :month";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['year' => $currentYear, 'month' => $currentMonth]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['next_number'];
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Insertar o actualizar persona
        $stmt = $pdo->prepare("INSERT INTO personas (id_cedula, nombre, apellido, email, telefono, iglesia) 
                              VALUES (:cedula, :nombre, :apellido, :email, :telefono, :iglesia)
                              ON CONFLICT (id_cedula) 
                              DO UPDATE SET 
                                nombre = EXCLUDED.nombre,
                                apellido = EXCLUDED.apellido,
                                email = EXCLUDED.email,
                                telefono = EXCLUDED.telefono,
                                iglesia = EXCLUDED.iglesia
                              RETURNING id_cedula");
                              
        $stmt->execute([
            'cedula' => $_POST['cedula'],
            'nombre' => $_POST['nombre'],
            'apellido' => $_POST['apellido'],
            'email' => $_POST['email'] ?? null,
            'telefono' => $_POST['telefono'] ?? null,
            'iglesia' => $_POST['iglesia']
        ]);
        
        $idPersona = $stmt->fetchColumn();
        
        // Insertar sobre
        $stmt = $pdo->prepare("INSERT INTO sobres (numero_sobre, fecha, id_persona) 
                              VALUES (:numero_sobre, CURRENT_DATE, :id_persona)
                              RETURNING id");
                              
        $stmt->execute([
            'numero_sobre' => getNextEnvelopeNumber($pdo),
            'id_persona' => $idPersona
        ]);
        
        $sobreId = $stmt->fetchColumn();
        
        // Insertar ofrendas
        if (isset($_POST['ofrendas']) && is_array($_POST['ofrendas'])) {
            $stmt = $pdo->prepare("INSERT INTO sobre_ofrendas 
                                  (id_sobre, id_tipo_ofrenda, id_tipo_moneda, monto) 
                                  VALUES (:id_sobre, :id_tipo_ofrenda, :id_tipo_moneda, :monto)");
            
            foreach ($_POST['ofrendas'] as $ofrenda) {
                if (!empty($ofrenda['tipo']) && !empty($ofrenda['monto']) && !empty($ofrenda['moneda'])) {
                    $stmt->execute([
                        'id_sobre' => $sobreId,
                        'id_tipo_ofrenda' => $ofrenda['tipo'],
                        'id_tipo_moneda' => $ofrenda['moneda'],
                        'monto' => floatval($ofrenda['monto'])
                    ]);
                }
            }
        }
        
        // Insertar transferencias
        if (isset($_POST['transferencias']) && is_array($_POST['transferencias'])) {
            $stmt = $pdo->prepare("INSERT INTO transferencias 
                                  (id_sobre, fecha_transf, num_transferencia, id_banco, 
                                   id_tipo_moneda, monto_transferencia, banco_otro) 
                                  VALUES (:id_sobre, :fecha_transf, :num_transferencia, 
                                   :id_banco, :id_tipo_moneda, :monto_transferencia, :banco_otro)");
            
            foreach ($_POST['transferencias'] as $transferencia) {
                if (!empty($transferencia['fecha']) && !empty($transferencia['numero']) && 
                    !empty($transferencia['monto']) && !empty($transferencia['moneda'])) {
                    
                    $idBanco = null;
                    $bancoOtro = null;
                    
                    if ($transferencia['banco'] === 'otro') {
                        $bancoOtro = $transferencia['banco_otro'];
                    } else {
                        $idBanco = $transferencia['banco'];
                    }
                    
                    $stmt->execute([
                        'id_sobre' => $sobreId,
                        'fecha_transf' => $transferencia['fecha'],
                        'num_transferencia' => $transferencia['numero'],
                        'id_banco' => $idBanco,
                        'id_tipo_moneda' => $transferencia['moneda'],
                        'monto_transferencia' => floatval($transferencia['monto']),
                        'banco_otro' => $bancoOtro
                    ]);
                }
            }
        }
        
        $pdo->commit();
        $_SESSION['message'] = "Sobre #" . getNextEnvelopeNumber($pdo) - 1 . " guardado exitosamente";
        header('Location: index.php');
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error al guardar el sobre: " . $e->getMessage();
    }
}

// Obtener catálogos para el formulario
$catalogos = obtenerCatalogos($pdo);
extract($catalogos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Diezmos y Ofrendas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
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

        

        <form id="sobreForm" method="POST" action="">
            <div class="form-header">
                <img src="assets/img/logo.png" alt="Logo Iglesia">
                <h1>Diezmos y Ofrendas</h1>
            </div>

            <div class="form-section">
                <h3>Datos del Miembro</h3>
                <div class="form-group">
                    <label for="cedula">Cédula:</label>
                    <div class="cedula-group">
                        <input type="text" id="cedula" name="cedula" required>
                        <button type="button" id="buscarPersona" class="btn-secondary">Buscar</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>

                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email">
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono">
                </div>

                <div class="form-group">
                    <label for="iglesia">Iglesia:</label>
                    <input type="text" id="iglesia" name="iglesia" required>
                </div>
            </div>

            <div class="form-section">
                <h3>Ofrendas</h3>
                <div id="ofrendasContainer">
                    <div class="ofrenda-item">
                        <div class="form-group">
                            <label for="tipo_ofrenda">Tipo de Ofrenda:</label>
                            <select name="ofrendas[0][tipo]" required>
                                <?php foreach ($tipos_ofrenda as $tipo): ?>
                                    <option value="<?php echo $tipo['id']; ?>">
                                        <?php echo htmlspecialchars($tipo['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="monto">Monto:</label>
                            <input type="number" step="0.01" name="ofrendas[0][monto]" required>
                        </div>

                        <div class="form-group">
                            <label for="moneda">Moneda:</label>
                            <select name="ofrendas[0][moneda]" required>
                                <?php foreach ($tipos_moneda as $moneda): ?>
                                    <option value="<?php echo $moneda['id']; ?>">
                                        <?php echo htmlspecialchars($moneda['codigo']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="button" id="addOfrenda" class="btn-secondary">Agregar Ofrenda</button>
            </div>

            <div class="form-section">
                <h3>Transferencias</h3>
                <div id="transferenciasContainer"></div>
                <button type="button" id="addTransferencia" class="btn-secondary">Agregar Transferencia</button>
            </div>

            <button type="submit" class="btn-submit">Guardar Sobre</button>
        </form>
    </div>

    <!-- Template para nueva ofrenda -->
    <template id="ofrendaTemplate">
        <div class="ofrenda-item">
            <div class="form-group">
                <label>Tipo de Ofrenda:</label>
                <select name="ofrendas[INDEX][tipo]" required>
                    <?php foreach ($tipos_ofrenda as $tipo): ?>
                        <option value="<?php echo $tipo['id']; ?>">
                            <?php echo htmlspecialchars($tipo['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Monto:</label>
                <input type="number" step="0.01" name="ofrendas[INDEX][monto]" required>
            </div>

            <div class="form-group">
                <label>Moneda:</label>
                <select name="ofrendas[INDEX][moneda]" required>
                    <?php foreach ($tipos_moneda as $moneda): ?>
                        <option value="<?php echo $moneda['id']; ?>">
                            <?php echo htmlspecialchars($moneda['codigo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="button" class="btn-remove">X</button>
        </div>
    </template>

    <!-- Template para nueva transferencia -->
    <template id="transferenciaTemplate">
        <div class="transferencia-item">
            <div class="form-group">
                <label>Fecha:</label>
                <input type="date" name="transferencias[INDEX][fecha]" required>
            </div>

            <div class="form-group">
                <label>Número:</label>
                <input type="text" name="transferencias[INDEX][numero]" required>
            </div>

            <div class="form-group">
                <label>Banco:</label>
                <select name="transferencias[INDEX][banco]" class="banco-select" required>
                    <?php foreach ($bancos as $banco): ?>
                        <option value="<?php echo $banco['id']; ?>">
                            <?php echo htmlspecialchars($banco['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="otro">Otro Banco...</option>
                </select>
                <input type="text" name="transferencias[INDEX][banco_otro]" 
                       class="banco-otro" style="display: none;">
            </div>

            <div class="form-group">
                <label>Monto:</label>
                <input type="number" step="0.01" name="transferencias[INDEX][monto]" required>
            </div>

            <div class="form-group">
                <label>Moneda:</label>
                <select name="transferencias[INDEX][moneda]" required>
                    <?php foreach ($tipos_moneda as $moneda): ?>
                        <option value="<?php echo $moneda['id']; ?>">
                            <?php echo htmlspecialchars($moneda['codigo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="button" class="btn-remove">X</button>
        </div>
    </template>

    <script src="assets/js/script.js"></script>
</body>
</html>