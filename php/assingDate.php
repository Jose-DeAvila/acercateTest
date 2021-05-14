<?php
  header('Content-Type: application/json');
  include "conexion.php";
  require "../vendor/autoload.php";
  use \Firebase\JWT\JWT;

  if(!$_COOKIE['Token']){
    echo json_encode(['msg'=>'Token not found in request or is invalid']);
    exit;
  }

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();

  $cod_cita = $_POST['cod_cita'];
  $token = $_COOKIE['Token'];
  $decode = JWT::decode($token, $_ENV['SECRET_KEY'], ['HS256']);
  

  $checkUserInCita = "SELECT * FROM cupos WHERE cod_cita=$cod_cita AND cod_usuario_solicitante=$decode->userId";
  $isInCita = $con->query($checkUserInCita);
  if($isInCita->fetch_assoc()){
    echo json_encode(['msg'=>'User is already in date.', 'code'=>409]);
    exit;
  }
   
  $query = "SELECT * FROM usuarios_roles where cod_usuario=$decode->userId AND cod_rol=1";
  $eje=$con->query($query);
  if($eje->fetch_assoc()){
    if(!preg_match('/Bearer\s(\S+)/', 'Bearer '.$token, $matches)){
      header('HTTP/1.0 400 Bad Request');
      echo 'Token not found in request or is invalid';
      exit;
    }

    $getCupos = "SELECT cupos_disponibles FROM citas WHERE cod = $cod_cita";
    $cupos=$con->query($getCupos);
    while($res = $cupos->fetch_assoc()){
      if($res['cupos_disponibles']>0){

        $insertCupo = "INSERT INTO cupos(cod_cita, cod_usuario_solicitante) VALUES ($cod_cita, $decode->userId)";
        $cupoInserted = $con->query($insertCupo);
        
        $newCupo = (int)$res['cupos_disponibles'] - 1;
        $updateCita = "UPDATE citas SET cupos_disponibles = $newCupo WHERE cod = $cod_cita";
        $citaUpdated = $con->query($updateCita);
        
        if($citaUpdated && $cupoInserted){
          echo json_encode(['msg'=>'Date reserved succesfully', 'code'=>201]);
          exit;
        }
        else{
          echo(mysqli_error($con));
        } 
      }
      else{
        echo json_encode(['msg'=>'This date is full, please choose another one', 'code'=>406]);
        exit;
      }
    }

    echo json_encode(['msg'=>'Date you\'re looking for doesn\'t exist.', 'code'=>404]);
    exit;
  }

  echo json_encode(['msg'=>'You aren\'t an solicitante.', 'code'=>401]);
