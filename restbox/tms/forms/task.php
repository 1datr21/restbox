<?php

use modules\restbox as restbox; 
use modules\restbox\obj_description as obj_description;
use modules\restbox\table\tfield as tfield;
use modules\restbox\table;

$info = new obj_description([
'fields'=>[
/*    'id'=>new ffield('hidden'),
    'name'=>new ffield('text',['maxlen'=>50]),
    'descr'=>new ffield('textbox',['maxlen'=>2500]),
    'options'=>new ffield('set',['values'=>['important','attached','group','slow'],'default'=>['slow','attached']]),
    'active'=>new ffield('bool'),
    'author'=>new ffield('ref',['table'=>'users']),
    'createdate'=>new ffield('datetime'),
    */
],
'events'=>[
    'beforeSave'=>function(&$row,&$rbenv,&$save) {      
        if(empty($row['id']))
        {
            $row['createdate']='#NOW()'; 
            $sess_vars = $rbenv->exe_mod_func('restbox.session','get_sess_vars'); 
            //print_dbg($sess_vars);
            $row['author']=$sess_vars['user_id'];
        }       
    },
    'onAccess'=>function(&$row,&$rbenv,&$save) {  

    }    
],
]);
?>