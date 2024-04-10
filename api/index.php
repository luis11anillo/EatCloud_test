<?php

$host = "localhost";
$usuario = "root";
$password = "";
$db = "dbdonantes_colombia";

$conexion = new mysqli($host, $usuario, $password, $db);

if ($conexion->connect_error) {
    die("Hubo un error en la conexion: " . $conexion->connect_error);
}

header("Content-Type: application/json");
$method = $_SERVER['REQUEST_METHOD'];

// Dividimos la URL
$url_segmentos = explode('/', $_SERVER['REQUEST_URI']);
$tablaName = $url_segmentos[2]; 

// Extraemos el nombre de la tabla en la URL
if (preg_match('/\btabla=([^&]+)/', $tablaName, $matches)) {
    $word = $matches[1]; 
    //var_dump($word);
} else {
    echo "";
}


switch ($method) {
    case 'GET':
        $tabla = $_GET['tabla'] ?? '';
        $id = $_GET['id'] ?? ''; 
        consulta($conexion, $tabla, $id);
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if ($word == 'puntos') {
            crearDonacion($conexion, $data); 
        }
        break;
    default: 
        echo "Metodo no permitido";
        break;
}

function puntoExiste($conexion, $id_punto) {
    $stmt = $conexion->prepare("SELECT * FROM puntos WHERE id_punto = ?");
    $stmt->bind_param("i", $id_punto);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    // Si no se encontró ningún resultado, devolvemos false
    if ($resultado->num_rows === 0) {
        return false;
    }
    
    // Obtenemos la fila correspondiente al ID del punto
    $fila = $resultado->fetch_assoc();
    
    $stmt->close();
    
    // Devolvemos la fila obtenida
    return $fila;
}

function consulta($conexion, $tabla, $id) {

    if ($tabla == '') {
    echo json_encode([
        "operacion" => false,
        "mensaje" => "Por favor, especifique el nombre de la tabla en la URL."
    ]);
    }

    $tabla = $conexion->real_escape_string($tabla);

    if ($id == 'todos') { 
        $sql = "SELECT * FROM $tabla";
    } else {  
        $id = $conexion->real_escape_string($id); 
        $sql = "SELECT * FROM $tabla WHERE id = '$id'";
    }

    $result = $conexion->query($sql);

    if ($result) {
        $datos = array();
        while ($fila = $result->fetch_assoc()) {
            $datos[] = $fila;
        }

        echo json_encode([
            "operacion" => true,
            "registros" => count($datos),
            "datos" => $datos,
        ]);
    } else {
        echo json_encode([
            "operacion" => false,
            "mensaje" => "Error al ejecutar la consulta: " . $conexion->error
        ]);
    }
}

function crearDonacion($conexion, $data) {
    
    //query a la tabla puntos buscando la columna id_punto $data->id_punto $punto
    //if $punto si existe sigue el proceso si no devuelve error de validacion (punto no valido)
    //inserta en la tabla detalles la data con la fecha actual $detalles
    //insertar en la tabla encabezados $detalles->campoCorrespondiente, $punto->datoCorrespondiente
    //devolver json compactando $punto, $detalles, $encabezados
    if (!isset($data['data']) || !is_array($data['data']) || empty($data['data'])) {
        echo json_encode([
            "operacion" => false,
            "mensaje" => "No se proporcionaron datos de donación válidos."
        ]);
        return;
    }

    $costoTotal = 0;
    $kgTotal = 0;
    foreach ($data['data'] as $detalle) {
        $id_punto = $detalle['id_punto'];
        $punto = puntoExiste($conexion, $id_punto);
        if ($punto === null) {
            echo json_encode([
                "operacion" => false,
                "mensaje" => "El punto con ID $id_punto no existe."
            ]);
            return;
        }
    
        try {
            $codigo_donacion = $detalle['codigo_donacion'];
            $id_punto = $detalle['id_punto'];
            $nombre_producto = $conexion->real_escape_string($detalle['nombre_producto']);
            $codigo_producto = $conexion->real_escape_string($detalle['codigo_producto']);
            $cantidad = $detalle['cantidad'];
            $kg_unitario = $detalle['kg_unitario'];
            $costo_unitario = $detalle['costo_unitario'];
    
            $detallesInsert = "INSERT INTO detalles (codigo_donacion, id_punto, nombre_producto, codigo_producto, cantidad, kg_unitario, costo_unitario, fecha_publicacion) VALUES ('$codigo_donacion', '$id_punto', '$nombre_producto', '$codigo_producto', '$cantidad', '$kg_unitario', '$costo_unitario', NOW())";


            $costoTotal += $costo_unitario;
            $kgTotal += $kg_unitario;
            $conexion->query($detallesInsert);
        } catch (\Throwable $th) {
            var_dump($th->getMessage());
        }
    }

    try {
        $namePunto = $punto['nombre_punto'];
        $idPunto = $punto['id_punto'];
        $dpto = $punto['departamento'];
        $city = $punto['ciudad'];
        $address = $punto['direccion'];
        $encabezadosInsert = "INSERT INTO encabezados (codigo_donacion, fecha_publicacion, kg_total, costo_total, nombre_punto, id_punto, departamento, ciudad, direccion) VALUES ('$codigo_donacion', NOW(), '$kgTotal', '$costoTotal', '$namePunto', '$idPunto', '$dpto', '$city', '$address')";
        $conexion->query($encabezadosInsert);
    } catch (\Throwable $th) {
        var_dump($th->getMessage());
    }

    echo json_encode([
        "mensaje" => "Donación creada exitosamente.",
        "operacion" => true,
        "detalles" => $detallesInsert,
        "encabezados" => $encabezadosInsert
    ]);

}

$conexion->close();
?>