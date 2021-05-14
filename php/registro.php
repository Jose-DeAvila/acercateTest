<?php 
  header('Content-Type: application/json');
  include "conexion.php";   
  
  $cod= $_POST['cod'];
  $razon=addslashes($_POST['razon_social']);
  $usuario= addslashes($_POST['usuario']);
  $clave= password_hash($_POST['clave'], PASSWORD_DEFAULT);
  
  if(!$_POST['cod'] || !$_POST['usuario'] || !$_POST['clave']){
    echo json_encode(['msg'=> 'Please, fill all fields', 'code'=> 400]);
    exit;
  }
  
  $sql = "SELECT * FROM usuarios where cod='$cod'";
	$eje = $con->query($sql);
  if($eje->fetch_assoc()){
    echo json_encode(['msg' => 'User is already registered', 'code'=> 409]);
  }
  else{
      $insert = "INSERT INTO usuarios (cod,usuario,clave,razon_social) VALUES ('$cod','$usuario','$clave', '$razon')";
      $con->query($insert);
      echo json_encode(['msg' => 'User created succesfully', 'code'=> 201]);
  }
