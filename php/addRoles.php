<?php 
  header('Content-Type: application/json');
  include "conexion.php";

  $cod=$_POST['cod'];
  $description = $_POST['description'];

  if(!$_POST['cod'] || !$_POST['description']){
    echo json_encode(['msg'=> 'Please, fill all fields', 'code'=> 400]);
    exit;
  }

  $sql = "SELECT cod from roles where cod='$cod'";
  $res = $con->query($sql);
  if($res->fetch_assoc()){
    echo json_encode(['msg'=>'Rol is already registered', 'code'=>409]);
    exit;
  }

  $query= "INSERT INTO roles(cod,descripcion) VALUES ('$cod','$description')";
  $eje = $con->query($query);
  if($eje){
    echo json_encode(['msg' => 'Rol creadted succesfully', 'code'=>203]);
  }
