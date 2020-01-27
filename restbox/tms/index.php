<?php
GLOBAL $_MUL_DBG_WORK;
GLOBAL $_BASEDIR;
$_MUL_DBG_WORK = true;

require_once url_seg_add($_BASEDIR,"dbconf.php");
$cfg=[
    'connections'=>[ $dbconn ],
    'usertable'=>'users', 
  /*  'session'=>[
        'max-exp'=>1000,
     //   'time-to-rename'=>25
    ],*/

];