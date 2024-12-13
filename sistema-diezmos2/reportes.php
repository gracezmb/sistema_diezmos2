<?php
// reportes.php
session_start();
require_once 'config/database.php';

function getReporte($pdo, $tipo, $fecha_inicio, $fecha_fin) {
    $sql = "WITH montos_por_tipo AS (
        SELECT 
            s.id,
            s.numero_sobre,
            s.fecha,
            p.nombre,
            p.apellido,
            tof.nombre as tipo_ofrenda,
            so.monto,
            tm.codigo as moneda
        FROM sobres s
        JOIN personas p ON s.id_persona = p.id_cedula
        JOIN sobre_ofrendas so ON s.id = so.id_sobre
        JOIN tipos_ofrenda tof ON so.id_tipo_ofrenda = tof.id
        JOIN tipos_moneda tm ON so.id_tipo_moneda = tm.id
        WHERE s.fecha BETWEEN :fecha_inicio AND :fecha_fin
    )
    SELECT 
        numero_sobre,
        fecha,
        nombre || ' ' || apellido as nombre,
        SUM(CASE WHEN tipo_ofrenda = 'Diezmo' THEN monto ELSE 0 END) as monto_diezmo,
        SUM(CASE WHEN tipo_ofrenda = 'Ofrenda de Pacto' THEN monto ELSE 0 END) as monto_ofrenda_amor,
        SUM(CASE WHEN tipo_ofrenda = 'Ofrenda de Colaboración' THEN monto ELSE 0 END) as monto_colaboracion,
        SUM(monto) as monto_total
    FROM montos_por_tipo
    GROUP BY numero_sobre, fecha, nombre, apellido
    ORDER BY fecha, numero_sobre";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'fecha_inicio' => $fecha_inicio,
        'fecha_fin' => $fecha_fin
    ]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTotales($reporte) {
    return [
        'total_diezmos' => array_sum(array_column($reporte, 'monto_diezmo')),
        'total_ofrendas_amor' => array_sum(array_column($reporte, 'monto_ofrenda_amor')),
        'total_colaboracion' => array_sum(array_column($reporte, 'monto_colaboracion')),
        'total_general' => array_sum(array_column($reporte, 'monto_total'))
    ];
}

$reporte = [];
$totales = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo_reporte'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    
    if ($tipo === 'semanal') {
        // Ajustar fecha_fin para que sea 7 días después de fecha_inicio
        $fecha_fin = date('Y-m-d', strtotime($fecha_inicio . ' +6 days'));
    }
    
    $reporte = getReporte($pdo, $tipo, $fecha_inicio, $fecha_fin);
    $totales = getTotales($reporte);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Diezmos y Ofrendas</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/reportes.css">
</head>
<body>
    <div class="container">
        <nav class="menu">
            <ul>
                <li><a href="index.php">Nuevo Sobre</a></li>
                <li><a href="reportes.php">Reportes</a></li>
            </ul>
        </nav>

        <div class="report-form">
            <h2>Generar Reporte</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="tipo_reporte">Tipo de Reporte:</label>
                    <select id="tipo_reporte" name="tipo_reporte" required>
                        <option value="semanal">Semanal</option>
                        <option value="mensual">Mensual</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fecha_inicio">Fecha Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                </div>

                <div class="form-group fecha-fin-group">
                    <label for="fecha_fin">Fecha Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin">
                </div>

                <button type="submit" class="btn-submit">Generar Reporte</button>
            </form>
        </div>

        <?php if (!empty($reporte)): ?>
            <div class="report-results">
                <h3>Resultados del Reporte</h3>
                <div class="report-info">
                    <p>Período: <?php echo date('d/m/Y', strtotime($fecha_inicio)); ?> - 
                               <?php echo date('d/m/Y', strtotime($fecha_fin)); ?></p>
                </div>

                <table class="report-table">
                    <thead>
                        <tr>
                            <th>N° Sobre</th>
                            <th>Fecha</th>
                            <th>Nombre</th>
                            <th>Diezmo</th>
                            <th>Ofrenda Amor</th>
                            <th>Colaboración</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reporte as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['numero_sobre']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['fecha'])); ?></td>
                                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td class="monto"><?php echo number_format($row['monto_diezmo'], 2); ?></td>
                                <td class="monto"><?php echo number_format($row['monto_ofrenda_amor'], 2); ?></td>
                                <td class="monto"><?php echo number_format($row['monto_colaboracion'], 2); ?></td>
                                <td class="monto"><?php echo number_format($row['monto_total'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Totales:</th>
                            <th class="monto"><?php echo number_format($totales['total_diezmos'], 2); ?></th>
                            <th class="monto"><?php echo number_format($totales['total_ofrendas_amor'], 2); ?></th>
                            <th class="monto"><?php echo number_format($totales['total_colaboracion'], 2); ?></th>
                            <th class="monto"><?php echo number_format($totales['total_general'], 2); ?></th>
                        </tr>
                    </tfoot>
                </table>

                <div class="report-actions">
                    <button onclick="window.print()" class="btn-print">Imprimir Reporte</button>
                    
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById('tipo_reporte').addEventListener('change', function() {
            const fechaFinGroup = document.querySelector('.fecha-fin-group');
            fechaFinGroup.style.display = this.value === 'mensual' ? 'block' : 'none';
            
            if (this.value === 'semanal') {
                document.getElementById('fecha_fin').removeAttribute('required');
            } else {
                document.getElementById('fecha_fin').setAttribute('required', 'required');
            }
        });

        function exportToExcel() {
            // Implementation for Excel export can be added here
            alert('Función de exportación a Excel en desarrollo');
        }
    </script>
</body>
</html>