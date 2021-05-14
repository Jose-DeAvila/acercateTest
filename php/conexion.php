<?php
    require '../vendor/autoload.php';
    
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $host=$_ENV['HOST'];
    $user=$_ENV['USER'];
    $pw=$_ENV['PASS'];
    $db=$_ENV['DATABASE'];
    $con = new mysqli($host,$user,$pw,$db);
