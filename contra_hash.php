<?php
// Contraseña que el usuario ingresó o la contraseña que quieres hashear
$password_en_texto_plano = "admin123";

// Hashear la contraseña
$hashed_password = password_hash($password_en_texto_plano, PASSWORD_DEFAULT);

echo "Contraseña en texto plano: " . $password_en_texto_plano . "<br>";
echo "Contraseña hasheada: " . $hashed_password;
?>