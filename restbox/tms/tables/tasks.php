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
        'onAccess'=>function($request,&$MLAM, &$do_it)
        {            
            $sess_id = $MLAM->_call_module('restbox.session','get_rb_token');
            if($request['path']=='tables/save')
            {
              //  print_dbg($sess_id);
                if(empty($sess_id))
                {
                    $do_it=false;
                ///    print_dbg($request);
                }
            }
            
            //print_dbg($do_it);
        }
    ],
]);
?>