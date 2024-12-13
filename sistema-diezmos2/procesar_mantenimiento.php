<?php
// procesar_mantenimiento.php
session_start();
require_once 'config/database.php';
header('Content-Type: application/json');

try {
    $tipo = $_POST['tipo'] ?? '';
    $accion = $_POST['accion'] ?? 'guardar';
    
    switch($accion) {
        case 'toggle':
            $id = $_POST['id'] ?? 0;
            if (!$id) {
                throw new Exception('ID no proporcionado');
            }
            
            $tabla = obtenerNombreTabla($tipo);
            $stmt = $pdo->prepare("UPDATE $tabla SET activo = NOT activo WHERE id = :id");
            $stmt->execute(['id' => $id]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'guardar':
            $id = $_POST['id'] ?? null;
            $tabla = obtenerNombreTabla($tipo);
            
            switch($tipo) {
                case 'moneda':
                    validarMoneda($_POST);
                    if ($id) {
                        $stmt = $pdo->prepare("UPDATE tipos_moneda 
                                             SET codigo = :codigo, nombre = :nombre, simbolo = :simbolo 
                                             WHERE id = :id");
                        $params = [
                            'id' => $id,
                            'codigo' => $_POST['codigo'],
                            'nombre' => $_POST['nombre'],
                            'simbolo' => $_POST['simbolo']
                        ];
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO tipos_moneda (codigo, nombre, simbolo) 
                                             VALUES (:codigo, :nombre, :simbolo)");
                        $params = [
                            'codigo' => $_POST['codigo'],
                            'nombre' => $_POST['nombre'],
                            'simbolo' => $_POST['simbolo']
                        ];
                    }
                    break;
                    
                case 'ofrenda':
                    validarOfrenda($_POST);
                    if ($id) {
                        $stmt = $pdo->prepare("UPDATE tipos_ofrenda 
                                             SET nombre = :nombre, descripcion = :descripcion 
                                             WHERE id = :id");
                        $params = [
                            'id' => $id,
                            'nombre' => $_POST['nombre'],
                            'descripcion' => $_POST['descripcion']
                        ];
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO tipos_ofrenda (nombre, descripcion) 
                                             VALUES (:nombre, :descripcion)");
                        $params = [
                            'nombre' => $_POST['nombre'],
                            'descripcion' => $_POST['descripcion']
                        ];
                    }
                    break;
                    
                case 'banco':
                    validarBanco($_POST);
                    if ($id) {
                        $stmt = $pdo->prepare("UPDATE bancos 
                                             SET nombre = :nombre, codigo = :codigo 
                                             WHERE id = :id");
                        $params = [
                            'id' => $id,
                            'nombre' => $_POST['nombre'],
                            'codigo' => $_POST['codigo']
                        ];
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO bancos (nombre, codigo) 
                                             VALUES (:nombre, :codigo)");
                        $params = [
                            'nombre' => $_POST['nombre'],
                            'codigo' => $_POST['codigo']
                        ];
                    }
                    break;
                    
                default:
                    throw new Exception('Tipo de registro no válido');
            }
            
            $stmt->execute($params);
            echo json_encode(['success' => true]);
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

// Funciones auxiliares
function obtenerNombreTabla($tipo) {
    switch($tipo) {
        case 'moneda':
            return 'tipos_moneda';
        case 'ofrenda':
            return 'tipos_ofrenda';
        case 'banco':
            return 'bancos';
        default:
            throw new Exception('Tipo de tabla no válido');
    }
}

function validarMoneda($data) {
    if (empty($data['codigo']) || strlen($data['codigo']) > 3) {
        throw new Exception('Código de moneda inválido');
    }
    if (empty($data['nombre'])) {
        throw new Exception('Nombre de moneda requerido');
    }
    if (empty($data['simbolo']) || strlen($data['simbolo']) > 5) {
        throw new Exception('Símbolo de moneda inválido');
    }
}

function validarOfrenda($data) {
    if (empty($data['nombre'])) {
        throw new Exception('Nombre de ofrenda requerido');
    }
}

function validarBanco($data) {
    if (empty($data['nombre'])) {
        throw new Exception('Nombre de banco requerido');
    }
    if (!empty($data['codigo']) && strlen($data['codigo']) > 20) {
        throw new Exception('Código de banco demasiado largo');
    }
}
?>