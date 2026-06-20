<?php
define('DB_HOST', 'sql200.infinityfree.com');
define('DB_USER', 'if0_42001944');
define('DB_PASS', 'e1eyZ2aVeX');
define('DB_NAME', 'if0_42001944_formulario');

function conectar() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die(json_encode(['error' => 'Error de conexion: ' . $conn->connect_error]));
    }
    $conn->set_charset('utf8');
    return $conn;
}
?>
