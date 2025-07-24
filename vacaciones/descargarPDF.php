<?php
global $link;
require_once '../config.php';
require_once 'generarPDF.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $colaborador_id = $_GET['colaborador_id'];
    $inicio = $_GET['fecha_inicio'];
    $fin = $_GET['fecha_fin'];

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="vacaciones.pdf"');

    generarPDF($link,$colaborador_id, $inicio, $fin);

}
