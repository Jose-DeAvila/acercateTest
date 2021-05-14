<?php 
  header('Content-Type: application/json');
  include "conexion.php";

  $cod_usuario = $_POST['cod_user'];  
  $cod = $_POST['cod'];
  
  if(!$_POST['cod_user']){
    echo json_encode(['msg'=>'Please, fill all fields', 'code'=>400]);
    exit;
  }
  

  if($_POST['cod']){
    
    $checkRol = "SELECT cod_usuario FROM usuarios_roles WHERE cod_usuario=$cod_usuario AND cod_rol=$cod";
    $resultCheck = $con->query($checkRol);
    if($resultCheck->fetch_assoc()){
      echo json_encode(['msg'=>'This user have this rol!', 'code'=>409]);
      exit;
    }

    $query = "INSERT INTO usuarios_roles(cod_usuario, cod_rol) VALUES ('$cod_usuario', '$cod')";
    $res = $con->query($query);

    $sql = "SELECT descripcion FROM roles where cod='$cod'";
    $eje2=$con->query($sql);
    while($res = $eje2->fetch_assoc()){
      if($res['descripcion'] == 'solicitante'){
        $addingSoli = "INSERT INTO solicitantes(cod_usuario) VALUES ('$cod_usuario')";
        $con->query($addingSoli); 
      }

      else if($res['descripcion'] == 'prestador'){
        $addingPres = "INSERT INTO prestadores(cod_usuario) VALUES ('$cod_usuario')";
        $con->query($addingPres);
      }
    }
    echo json_encode(['msg'=> 'User rol added succesfully', 'code'=>203]);
    exit;
  }

  $checkRol = "SELECT cod_rol FROM usuarios_roles WHERE cod_usuario=$cod_usuario";
  $resultCheck = $con->query($checkRol);
  if($resultCheck->fetch_assoc()){
    echo json_encode(['msg'=> 'User already have a rol. Insert a rol code', 'code'=>409]);
    exit;
  }

  $searchRoles = "SELECT cod from roles";
  $eje = $con->query($searchRoles);
  
  while($res = $eje->fetch_assoc()){
    $query = "INSERT INTO usuarios_roles(cod_usuario, cod_rol) VALUES (".$cod_usuario.",".$res['cod'].")";
    $con->query($query);
  }

  $addingSoli = "INSERT INTO solicitantes(cod_usuario) VALUES ('$cod_usuario')";
  $con->query($addingSoli);

  $addingPres = "INSERT INTO prestadores(cod_usuario) VALUES ('$cod_usuario')";
  $con->query($addingPres);
 
  echo json_encode(['msg'=>'User roles added succesfully', 'code' => 203]);
