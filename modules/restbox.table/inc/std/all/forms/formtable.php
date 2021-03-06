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
            $newrow = $table_map->get_new_row();
            return $newrow;
        }
        else
        {
            $_id = $env->_ROUTE_PARAMS['route_pieces'][0];
            $_table = $env->_ROUTE_PARAMS['object']['name'];
            $table_map = $env->P_MODULE->exe_mod_func('restbox.table', 'load_table',$_table);
            $item = $table_map->get_item($_id);
        
            if($item==null)
            {
              // generate error
              $this->P_MODULE->exe_mod_func('restbox','out_error',['message'=>"Page is not exists",'errno'=>404]);	
            }
            return $item;
            
        }
        
        return [];
    },
    'OnAccess'=>function(&$rbenv, $_data=[])
    {
        $sess_vars = $rbenv->P_MODULE->exe_mod_func('restbox.session','get_sess_vars'); 

        if(!isset($sess_vars['user_id']))
            return true;
    },
    'OnSubmit'=>function($env,$_p_data) { // add or edit object
        $_table = $env->_ROUTE_PARAMS['object']['name'];
        $table_map = $env->P_MODULE->exe_mod_func('restbox.table', 'load_table',$_table); 
        $id_fld_name =  $table_map->get_id_field();
        if(isset($_p_data[$id_fld_name]))// existing object
        {
            $obj = $table_map->get_item($_p_data[$id_fld_name]);
         //   print_dbg($obj);
        }
        else
        {
           $newobj = $table_map->save($_p_data);
           //print_dbg($newobj);
        }
     //   print_dbg($id_fld_name);
        //print_dbg($_p_data);
        //print_dbg($table_map);
        //$table_map->get_item($_id_val);
    },
    'OnValidate'=>function($env,$_p_data) {
        $_table = $env->_ROUTE_PARAMS['object']['name'];
        $table_map = $env->P_MODULE->exe_mod_func('restbox.table', 'load_table',$_table);
        return $table_map->validate($_p_data);
    }
],
]);
?>