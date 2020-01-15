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
        'options'=>new tfield('set',['values'=>['important','attached','group','slow'],'default'=>['slow','attached']]),
        'active'=>new tfield('bool'),
        'author'=>new tfield('ref',['table'=>'users']),
        'createdate'=>new tfield('datetime'),
    ],
    'events'=>[
        'beforeSave'=>function(&$row,&$save) {      
            if(empty($row['id']))
                $row['createdate']='#NOW()';        
        },
        'onAccess'=>function($request,&$PMODULE, &$do_it)
        {            
            $_sess_id = $PMODULE->exe_mod_func('restbox.session','get_rb_token');
            if($request['path']=='tables/save')
            {
                if($_sess_id==null)
                {
                    $do_it=false;
                }
            }
        }
    ],
]);
?>