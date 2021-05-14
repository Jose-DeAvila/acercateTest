<?php
  header("Content-Type: application/json");

  if($_COOKIE['Token']){
    setcookie("Token", "", time()-3600);
    echo json_encode(['msg'=>'The session was closed successfully!', 'code'=>200]);
    exit;
  }

  else{
    echo json_encode(['msg'=>'No user currently logged in!', 'code'=>410]);
  }
