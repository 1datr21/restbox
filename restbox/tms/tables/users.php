<?php

    use modules\restbox as restbox; 
    use modules\restbox\obj_description as obj_description;
    use modules\restbox\table\tfield as tfield;
    use modules\restbox\table;

    $info = new obj_description([
    'fields'=>[
        'id'=>new tfield('id'),
        'login'=>new tfield('text',['maxlen'=>20]),
        'email'=>new tfield('text',['maxlen'=>50]),
        'passw'=>new tfield('password'),
        'status'=>new tfield('enum',['values'=>['student','prepod','admin'],'default'=>'student']),
        'birthday'=>new tfield('datetime'),
        'avatar'=>new tfield('file',['mode'=>'blob']),
   
    ]
]);


?>