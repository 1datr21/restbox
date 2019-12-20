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
    ]
]);
?>