<?php
require_once __DIR__ . '/../config.php';

class Vacaciones {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function registrar($colaborador_id, $inicio, $fin) {

        if ($this->existeTraslape($colaborador_id, $inicio, $fin)) {
            return ['status' => false, 'error' => 'traslape'];
        }
        // 1. Insertar nueva solicitud de vacaciones
        $stmt = $this->conn->prepare("INSERT INTO vacaciones (colaborador_id, fecha_inicio, fecha_fin) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $colaborador_id, $inicio, $fin);

        if (!$stmt->execute()) {
            return ['status' => false, 'error' => 'insert_error'];
        }
        $stmt->close();

        $fecha_actual = date('Y-m-d H:i:s');
        if ($inicio == $fecha_actual){
            $estado_id = 1;
            // 2. Actualizar estado de colaborador a vacaciones
            $update = $this->conn->prepare("UPDATE colaboradores SET estatus_id = ? WHERE id_colaborador = ?");
            $update->bind_param("ii", $estado_id, $colaborador_id);

            if (!$update->execute()) {
                return false; // Error en el update
            }

            $update->close();
        }

        return ['status' => true];
    }


    public function obtenerDiasDisponibles($colaborador_id) {
        $stmt = $this->conn->prepare("SELECT fecha_ingreso FROM colaboradores WHERE id_colaborador = ?");
        $stmt->bind_param("i", $colaborador_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $colaborador = $resultado->fetch_assoc();

        if (!$colaborador) return 3;

        $fechaIngreso = new DateTime($colaborador['fecha_ingreso']);
        $fechaActual = new DateTime();
        $intervalo = $fechaIngreso->diff($fechaActual);

        $diasDisponibles = floor($intervalo->days / 11);

        $stmt2 = $this->conn->prepare("SELECT SUM(DATEDIFF(fecha_fin, fecha_inicio) + 1) AS usados FROM vacaciones WHERE colaborador_id = ?");
        $stmt2->bind_param("i", $colaborador_id);
        $stmt2->execute();
        $resultado2 = $stmt2->get_result();
        $row = $resultado2->fetch_assoc();
        $usados = $row['usados'] ?? 0;

        return max(0, $diasDisponibles - $usados);
    }

    public function existeTraslape($colaborador_id, $inicio, $fin) {
        $stmt = $this->conn->prepare("
        SELECT COUNT(*) as traslape 
        FROM vacaciones 
        WHERE colaborador_id = ? 
        AND (
            (? BETWEEN fecha_inicio AND fecha_fin) OR
            (? BETWEEN fecha_inicio AND fecha_fin)
        )
    ");
        $stmt->bind_param("iss", $colaborador_id, $inicio, $fin);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();

        return $row['traslape'] > 0;
    }

}

