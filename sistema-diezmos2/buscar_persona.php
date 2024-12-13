<?php
// buscar_persona.php
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_GET['cedula'])) {
    echo json_encode(['success' => false, 'error' => 'Cédula no proporcionada']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM personas WHERE id_cedula = :cedula AND activo = true");
    $stmt->execute(['cedula' => $_GET['cedula']]);
    $persona = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($persona) {
        echo json_encode([
            'success' => true,
            'persona' => [
                'nombre' => $persona['nombre'],
                'apellido' => $persona['apellido'],
                'email' => $persona['email'],
                'telefono' => $persona['telefono'],
                'iglesia' => $persona['iglesia']
            ]
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>