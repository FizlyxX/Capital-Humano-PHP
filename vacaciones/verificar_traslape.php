<?php

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'errores.log');

global $link;
require_once 'Vacaciones.php';
require_once 'generarPDF.php';
require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $colaborador_id = $_POST['colaborador_id'];
    $inicio = $_POST['fecha_inicio'];
    $fin = $_POST['fecha_fin'];

    $vacaciones = new Vacaciones($link);
    $respuesta = $vacaciones->registrar($colaborador_id, $inicio, $fin);

    if (!$respuesta['status']) {
        if ($respuesta['error'] === 'traslape') {
            header('Content-Type: application/json');
            echo json_encode($respuesta);
        }
    }else{
        header('Content-Type: application/json');
        echo json_encode($respuesta);
    }
}

