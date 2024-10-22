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
        // Extraer los datos
        $cedula_cliente = $datos['cedula_cliente'];
        $nombre_cliente = $datos['nombre_cliente'];
        $correo_cliente = $datos['correo_cliente'];
        $celular_cliente = $datos['celular_cliente'];

        // Verificar si el cliente ya está registrado (por cédula)
        $stmt_verificar = $conexion->prepare("SELECT cedula_cliente FROM cliente WHERE cedula_cliente = ?");
        $stmt_verificar->bind_param("s", $cedula_cliente);
        $stmt_verificar->execute();
        $stmt_verificar->store_result();

        if ($stmt_verificar->num_rows > 0) {
            // Si ya existe el cliente, devolver el mensaje correspondiente
            echo json_encode(["mensaje" => "Usuario ya existente"]);
        } else {
            // Preparar la consulta SQL para insertar el cliente si no existe
            $stmt_insertar = $conexion->prepare("INSERT INTO cliente (cedula_cliente, nombre_cliente, correo_cliente, celular_cliente) 
                                        VALUES (?, ?, ?, ?)");
            $stmt_insertar->bind_param("ssss", $cedula_cliente, $nombre_cliente, $correo_cliente, $celular_cliente);

            // Ejecutar la consulta de inserción
            if ($stmt_insertar->execute()) {
                echo json_encode(["mensaje" => "Cliente registrado exitosamente"]);
            } else {
                echo json_encode(["mensaje" => "Error al registrar el cliente: " . $stmt_insertar->error]);
            }

            // Cerrar la sentencia de inserción
            $stmt_insertar->close();
        }

        // Cerrar la sentencia de verificación
        $stmt_verificar->close();
    } else {
        // Responder con error si los datos no son válidos
        echo json_encode(["mensaje" => "Datos inválidos o incompletos"]);
    }
} else {
    // Responder con error si no es una solicitud POST válida
    echo json_encode(["mensaje" => "Solicitud no válida o malformada"]);
}

// Cerrar la conexión
$conexion->close();
?>
