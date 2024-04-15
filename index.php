<?php
$host="localhost";
$user="root";
$password="";
$database="api";

$conexion = new mysqli($host,$user,$password,$database);

if($conexion->connect_error){
    die("Conexion no establecida".$conexion->connect_error);
    
}

header("Content-Type: application/json");
$metodo = $_SERVER['REQUEST_METHOD'];
//print_r($metodo);
//imprime el metodo utilizado
$path= isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'/';

$buscarId=explode('/', $path);

$id = ($path!=='/') ? end($buscarId):null;


switch($metodo){
    //Consulta SELECT
    case 'GET':
        consulta($conexion,$id);
        break;
    //INSERT
    case 'POST':
        insertar($conexion);
        break;
    //UPDATE
    case 'PUT':
        actualizar($conexion,$id);
        break;
    //DELETE
    case 'DELETE':
        borrar($conexion,$id);
        break;
    default:
        echo "Método no permitido";
        break;
} 

function consulta($conexion,$id){
    $sql= ($id===null) ? "SELECT id FROM usuarios":"SELECT * FROM usuarios WHERE id=$id";
    $resultado = $conexion->query($sql);
    
    if($resultado){
        $dato = array();
        while($fila = $resultado->fetch_assoc()){
            $datos[]=$fila;
        }
        echo json_encode($datos);
    }
}

function insertar($conexion){

    $dato= json_decode(file_get_contents('php://input'),true);
    $nombre= $dato['nombre'];
    //print_r($nombre);
    
    $sql = "INSERT INTO usuarios(nombre) VALUES ('$nombre')";
    $resultado = $conexion->query($sql);

    if($resultado){
        $dato['id'] = $conexion ->insert_id;
        echo json_encode($dato);
    }else{

        echo json_encode(array('error'=>'Error al crear usuario'));
    }

}

function borrar($conexion,$id){
    $sql = "DELETE FROM usuarios WHERE id = $id";
    $resultado = $conexion->query($sql);

    if($resultado){
        echo json_encode(array('mensaje'=>'Usuario borrado'));
    }else{
        echo json_encode(array('error'=>'Error al borrar el usuario'));
    }


}

function actualizar($conexion,$id){

    $dato= json_decode(file_get_contents('php://input'),true);
    $nombre= $dato['nombre'];

    echo "El id a editar es: ".$id. " con el dato ".$nombre;

    $sql = "UPDATE usuarios SET nombre = '$nombre' WHERE id = $id";
    $resultado = $conexion->query($sql);
   

    if($resultado){
        echo json_encode(array('mensaje'=>'Usuario actualizado'));
    }else{
        echo json_encode(array('error'=>'Error al actualizar el usuario'));
    }

}
?>