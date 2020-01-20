<?php
GLOBAL $_MUL_DBG_WORK;
$_MUL_DBG_WORK = true;
require_once "./dbconf.php";
$cfg=[
    'connections'=>[ $dbconn ],
    'usertable'=>'users', 
  /*  'session'=>[
        'max-exp'=>1000,
     //   'time-to-rename'=>25
    ],*/

];