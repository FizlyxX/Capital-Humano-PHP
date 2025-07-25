<?php
require_once '../config.php';
function colaboradorXSexo()
{
    global $link;
    $query = "SELECT sexo, COUNT(*) as cantidad FROM colaboradores WHERE activo = 1 GROUP BY sexo";
    $result = mysqli_query($link, $query);

    $masculino = 0;
    $femenino = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $sexo = strtolower($row['sexo']);
        if ($sexo === 'm') {
            $masculino = (int)$row['cantidad'];
        } elseif ($sexo === 'f') {
            $femenino = (int)$row['cantidad'];
        }
    }

// Enviar respuesta JSON
    return array(
        "masculino" => $masculino,
        "femenino" => $femenino
    );

}


