<?php
use modules\restbox as restbox; 
use modules\restbox\obj_description as obj_description;
use modules\restbox\table\tfield as tfield;
use modules\restbox\table;

$info = new obj_description([
'fields'=>[
    'login'=>'text',
    'passw'=>'text'
/*    'id'=>new ffield('hidden'),
    'name'=>new ffield('text',['maxlen'=>50]),
    'descr'=>new ffield('textbox',['maxlen'=>2500]),
    'options'=>new ffield('set',['values'=>['important','attached','group','slow'],'default'=>['slow','attached']]),
    'active'=>new ffield('bool'),
    'author'=>new ffield('ref',['table'=>'users']),
    'createdate'=>new ffield('datetime'),
    */
],
//'table'=>'tasks',
'events'=>[
    'OnSubmit'=>function($_p_data) {  

        $auth_res = $this->P_MODULE->login($_p_data);   
      //  print_dbg("log in >>");
      //  print_dbg($_p_data);
        return $auth_res;
    },
    'OnValidate'=>function($_p_data) {
        print_dbg('Validating form');
    }
],
]);
?>