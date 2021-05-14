<?php 
  header('Content-Type: application/json');
  include "conexion.php";
  require '../vendor/autoload.php';
  use \Firebase\JWT\JWT;

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();
  
  $usuario= $_POST['usuario'];
  $clave= $_POST['clave'];

  $query = "SELECT * FROM usuarios where usuario='$usuario'";
  $eje=$con->query($query);

  while($res=$eje->fetch_assoc()){
    if(password_verify($clave, $res['clave'])){
      $issuedAt = new DateTimeImmutable();
      $expire = $issuedAt->modify('+60 minutes')->getTimestamp();
      $serverName = 'localhost';

      $payload = array(
      "iat"=>$issuedAt->getTimestamp(),
      "iss"=>$serverName,
      "nbf"=>$issuedAt->getTimestamp(),
      "exp"=>$expire,
      "userName"=>$res['usuario'],
      "userId"=>$res['cod']
      );

      $jwt = JWT::encode($payload, $_ENV['SECRET_KEY'], 'HS256');
      $cookie = array(
        'token'=>$jwt,
        'msg'=>'User logged succesfully',
        'code'=>202,
      );

      echo json_encode($cookie);
      setcookie("Token",$jwt, time()*60, 'localhost');
      exit;
    }
    echo json_encode(['msg'=>'Invalid username or password', 'code'=>401]);
    exit;
  } 

  echo json_encode(['msg'=>'Invalid username or password', 'code'=>401]);
?>
