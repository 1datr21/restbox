<?php
$info=[
    'fields'=>[
        'id'=>new tfield('id'),
        'name'=>new tfield('text',['maxlen'=>50])),
        'descr'=>new tfield('bigtext',['maxlen'=>2500]),
        'passw'=>new tfield('password'),
        'author'=>new tfield('ref',['table_to'=>'users']),
    ]
];
?>