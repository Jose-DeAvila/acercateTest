<?php
  header('Content-Type: application/json');
  include "conexion.php";
  require '../vendor/autoload.php';
  use \Firebase\JWT\JWT;
  
  if(!$_COOKIE['Token']){
    echo json_encode(['msg'=>'Token not found in request or is invalid']);
    exit;
  }

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();

  $cod = $_POST['cod'];
  $descripcion = $_POST['descripcion'];
  $cupos_totales = $_POST['cupos_totales'];
  $fecha = date('Y/m/d');
  $token = $_COOKIE['Token'];
  $decode = JWT::decode($token, $_ENV['SECRET_KEY'], array('HS256'));
  $userId = $decode->userId;
  
  $query = "SELECT * FROM usuarios_roles WHERE cod_usuario = '$decode->userId' AND cod_rol = 2";
  $eje=$con->query($query);
  if($eje->fetch_assoc()){
    if (! preg_match('/Bearer\s(\S+)/', 'Bearer '.$token, $matches)) {
      header('HTTP/1.0 400 Bad Request');
      echo 'Token not found in request or is invalid';
      exit;
    }

    $insert = "INSERT INTO citas(cod,descripcion,cupos_totales,cupos_disponibles,cod_usuario_prestador, fecha) VALUES ('$cod','$descripcion',$cupos_totales,$cupos_totales,'$decode->userId', DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL 1 DAY))";
    $res=$con->query($insert);
    if($res){
      echo json_encode(['msg'=>'Dates created succesfully', 'code'=>201]);
      exit;
    }
    else{
      echo(mysqli_error($con));
    }
  } 
  else{
    echo json_encode(['msg'=>'User is not Prestador', 'code'=>401]);
  }
