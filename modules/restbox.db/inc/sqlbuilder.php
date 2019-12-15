<?php

namespace modules\restbox\db {
    use Core;
 
    class SQLBuilder {
        function q_select($args)
        {
            $q_field_list = "";
            $sql = "SELECT {$q_field_list} FROM {$args['table']}";
            return $sql;
        }
    }
}
