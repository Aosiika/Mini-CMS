<?php
// Generar nuevo hash para la contraseña "1"
$password = "1";
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: " . $password . "\n";
echo "Hash generado: " . $hash . "\n";

// Generar la consulta SQL
echo "\nConsulta SQL para actualizar:\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE username = 'admin';\n";
?> 