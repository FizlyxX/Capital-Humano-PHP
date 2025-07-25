<?php

class Sanitizar
{
    /**
     * Sanea una cadena de texto eliminando espacios, barras y caracteres HTML.
     * Es ideal para campos de texto generales.
     * @param string $data La cadena a sanear.
     * @return string La cadena saneada.
     */
    public static function sanearString($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    /**
     * Sanea un correo electrónico utilizando un filtro de PHP.
     * @param string $email El correo a sanear.
     * @return string El correo saneado.
     */
    public static function sanearEmail($email)
    {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Valida que un campo no esté vacío.
     * @param mixed $data El dato a validar.
     * @return bool True si el dato no está vacío, false en caso contrario.
     */
    public static function validarCampoNoVacio($data)
    {
        return !empty(trim($data));
    }
    
    /**
     * Valida el formato de un correo electrónico.
     * @param string $email El correo a validar.
     * @return bool True si el correo tiene un formato válido, false en caso contrario.
     */
    public static function validarEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Valida el formato de una fecha (Y-m-d).
     * @param string $date La fecha a validar.
     * @return bool True si la fecha tiene el formato correcto, false en caso contrario.
     */
    public static function validarFecha($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Valida que la identificación no esté ya registrada para otro colaborador.
     * @param mysqli $link Objeto de conexión a la base de datos.
     * @param string $identificacion La identificación a verificar.
     * @param int $id_colaborador El ID del colaborador actual para excluir.
     * @return bool True si la identificación es única, false en caso contrario.
     */
    public static function validarIdentificacionUnica($link, $identificacion, $id_colaborador)
    {
        $sql_check_id = "SELECT id_colaborador FROM colaboradores WHERE identificacion = ? AND id_colaborador != ?";
        if ($stmt_check_id = mysqli_prepare($link, $sql_check_id)) {
            mysqli_stmt_bind_param($stmt_check_id, "si", $identificacion, $id_colaborador);
            if (mysqli_stmt_execute($stmt_check_id)) {
                mysqli_stmt_store_result($stmt_check_id);
                $is_unique = (mysqli_stmt_num_rows($stmt_check_id) == 0);
                mysqli_stmt_close($stmt_check_id);
                return $is_unique;
            }
            mysqli_stmt_close($stmt_check_id);
        }
        return false;
    }
}