<?php

    use modules\restbox as restbox; 
    use modules\restbox\obj_description as obj_description;
    use modules\restbox\table\tfield as tfield;
    use modules\restbox\table;

    $info = new obj_description([
    'fields'=>[
        'id'=>new tfield('id'),
        'login'=>new tfield('text',['maxlen'=>20],'require'),
        'email'=>new tfield('text',['maxlen'=>50],'require'),
        'passw'=>new tfield('password','require'),
        'status'=>new tfield('enum',['values'=>['student','prepod','admin'],'default'=>'student']),
        'birthday'=>new tfield('datetime'),
        'regdate'=>new tfield('datetime'),
        'avatar'=>new tfield('file',['mode'=>'blob']),
    ],
    'events'=>[
        'beforeSave'=>function(&$row,&$save) {      
            if(empty($row['id']))
                $row['regdate']='#NOW()';        
        },
        'onAccess'=>function($request,&$MLAM, &$do_it)
        {            
            $sess_id = $MLAM->exe_mod_func('restbox.session','get_rb_token');
            if($request['path']=='save')
            {
                if($sess_id==null)
                {
                    $do_it=false;
                }
            }
            //print_dbg($request);
            print_dbg($sess_id);
        }
    ],
    'addinfo'=>['authroles'=>[
        'login'=>'login',
      //  'password'=>'passw',
        'email'=>'email']]
]);


?>