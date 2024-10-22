<?php
// Datos de conexión a la base de datos
$host = "localhost"; 
$user = "root"; 
$password = "Mvxf8BUuo9sq*bpQ"; 
$database = "sistema_ejemplo";

// Crear la conexión
$conexion = new mysqli($host, $user, $password, $database);

// Verificar la conexión
if ($conexion->connect_error) {
    die(json_encode(["mensaje" => "Error en la conexión: " . $conexion->connect_error]));
}

// Encabezados para permitir CORS y trabajar con JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Verificar si se recibieron datos en formato JSON
if ($_SERVER["REQUEST_METHOD"] == "POST" && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    // Obtener los datos del cuerpo de la solicitud
    $datos = json_decode(file_get_contents('php://input'), true);

    if ($datos) {
        // Extraer los datos del formulario
        $cedula_cliente = $datos['cedula_cliente'];
        $correo_cliente = $datos['correo_cliente'];

        // Verificar si el usuario existe en la base de datos
        $stmt = $conexion->prepare("SELECT nombre_cliente FROM cliente WHERE cedula_cliente = ? AND correo_cliente = ?");
        $stmt->bind_param("ss", $cedula_cliente, $correo_cliente);
        $stmt->execute();
        $stmt->bind_result($nombre_cliente);
        $stmt->fetch();

        // Si se encuentra al usuario, devolver el nombre y mensaje de bienvenida
        if ($nombre_cliente) {
            echo json_encode(["mensaje" => "Bienvenido, $nombre_cliente"]);
        } else {
            echo json_encode(["mensaje" => "Usuario o correo incorrecto"]);
        }

        // Cerrar la sentencia preparada
        $stmt->close();
    } else {
        // Responder con error si los datos no son válidos
        echo json_encode(["mensaje" => "Datos inválidos"]);
    }
} else {
    // Responder con error si no es una solicitud POST válida
    echo json_encode(["mensaje" => "Solicitud no válida"]);
}

// Cerrar la conexión
$conexion->close();
?>
