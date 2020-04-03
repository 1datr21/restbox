<?php

    use modules\restbox as restbox; 
    use modules\restbox\obj_description as obj_description;
    use modules\restbox\table\tfield as tfield;
    use modules\restbox\table;

    $info = new obj_description([
    'fields'=>[
        'id'=>new tfield('id'),
        'name'=>new tfield('text',['maxlen'=>50]),
        'descr'=>new tfield('bigtext',['maxlen'=>2500]),
        'state'=>new tfield('set',['values'=>['primary','agreed','usual','_important','emergency'],'default'=>'primary']),
        'options'=>new tfield('set',['values'=>['important','attached','group','slow'],'default'=>['slow','attached']]),
        'active'=>new tfield('bool'),
        'author'=>new tfield('ref',['table'=>'users']),
        'createdate'=>new tfield('datetime'),
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
        
    ],
]);
?>