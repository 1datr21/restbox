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
        }
    ]
]);


?>