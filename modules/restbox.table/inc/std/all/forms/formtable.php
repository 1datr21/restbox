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
//'table'=>'tasks',
'events'=>[
    'OnInit'=>function($env)
    {
        if(count($env->_ROUTE_PARAMS['route_pieces'])==0)
        {
            $_table = $env->_ROUTE_PARAMS['object']['name'];
            $table_map = $env->P_MODULE->exe_mod_func('restbox.table', 'load_table',$_table);
          //  print_dbg($table_map);
            $newrow = $table_map->get_new_row();
            print_dbg($newrow);
            return $newrow;
        }
        // [_ROUTE_PARAMS]  [object]  [name]
        
        return [];
      //print_dbg($env);
    },
    'OnSubmit'=>function($env,$_p_data) { 
      //  return $env->P_MODULE->exe_mod_func('restbox.session','logout');   
    },
    'OnValidate'=>function($env,$_p_data) {
       // print_dbg('Validating form');
    }
],
]);
?>