<?php
GLOBAL $_MUL_DBG_WORK;
$_MUL_DBG_WORK = true;
require_once "./dbconf.php";
$cfg=[
    'connection'=>$dbconn,
    'usertable'=>'users', 

];