<?php

use modules\restbox as restbox; 
use modules\restbox\obj_description as obj_description;
use modules\restbox\table\tfield as tfield;
use modules\restbox\table;

$info = new obj_description([
'fields'=>[
    'name'=>'text',
    'descr'=>'longtext'
/*    'id'=>new ffield('hidden'),
    'name'=>new ffield('text',['maxlen'=>50]),
    'descr'=>new ffield('textbox',['maxlen'=>2500]),
    'options'=>new ffield('set',['values'=>['important','attached','group','slow'],'default'=>['slow','attached']]),
    'active'=>new ffield('bool'),
    'author'=>new ffield('ref',['table'=>'users']),
    'createdate'=>new ffield('datetime'),
    */
],
'table'=>'tasks',
'events'=>[
    'OnSubmit'=>function($_p_data) {      
        print_dbg("The form is submited>>");
        print_dbg($_p_data);
        return $_p_data;
    },
    'OnValidate'=>function($_p_data) {
        $_res=[];
        if(empty($_p_data['name']))
        {
            $_res['name']='Name could not be empty';
        }
        print_dbg('Validating');
        return $_res;
    }
],
]);
?>