<?php
/*    $dbconn = [
        'drv'=>'mysql',
        'host'=>'p:localhost',
        'user'=>'root',
//        'passw'=>'',
        'passw'=>'123456',
        'dbname'=>'tmsus',
        'prefix' =>"tms_",
        'create_if_not_exists'=>true,
        'charset'=>"utf8",
        'collation'=>'utf8_general_ci',
    ];
   */
  
    $host = 'localhost';
    $db = 'tms';
    $charset='utf8';
    $user='root';    
   // $passw='';    
    $passw='root';
    $dbconn = [
        'drv'=>'pdo',
        'prefix' =>"tms_",
        'connstr'=>"mysql:host=$host;dbname=$db;charset=$charset",
        //'connstr'=>"mysql:host=$host;dbname=$db",
        'user'=>$user,
        'passw'=>$passw,
        'create_if_not_exists'=>true,
        'charset'=>"utf8",
       // 'charset'=>"utf8mb4",
        'collation'=>'utf8_general_ci',
        //'collation'=>'utf8mb4_0900_ai_ci',
    ];
    
?>