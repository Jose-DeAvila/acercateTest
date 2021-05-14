<?php 
  header('Content-Type: application/json');
  include "conexion.php";
  use \Firebase\JWT\JWT;
  require '../vendor/autoload.php';  

  if(!$_COOKIE['Token']){
    echo json_encode(['msg'=>'Token not found in request or is invalid']);
    exit;
  }

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();
  
  $cod_prestador = $_POST['cod_prestador'];
  $token = $_COOKIE['Token'];
  $decode = JWT::decode($token, $_ENV['SECRET_KEY'], ['HS256']);
  
  if($cod_prestador == $decode->userId){
    echo json_encode(['msg'=>'You can\'t subscribe yourself!', 'code'=>409]);
    exit;
  }

  $query = "SELECT * FROM usuarios_roles where cod_usuario='$decode->userId' AND cod_rol=1";
  $eje=$con->query($query);
  if($eje->fetch_assoc()){
    if(!preg_match('/Bearer\s(\S+)/', 'Bearer '.$token, $matches)){
      header('HTTP/1.0 400 Bad Request');
      echo 'Token not found in request or is invalid';
      exit;
    }

    $insert = "INSERT INTO solicitantes_prestadores(cod_usuario_solicitante, cod_usuario_prestador) VALUES ('$decode->userId','$cod_prestador')";
    $res=$con->query($insert);
    if($res){
      echo json_encode(['msg'=>'Subscribed succesfully to '.$cod_prestador, 'code'=>202]);
      exit;
    } 
    else{
      echo json_encode(['msg'=>'An error has ocurred in the process', 'code'=>500]);
      exit;
    }
  }
  echo json_encode(['msg'=>'User is not solicitante', 'code'=>401]);
