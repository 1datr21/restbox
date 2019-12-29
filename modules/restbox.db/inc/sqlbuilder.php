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
/*
[fld=> , type=>{=,<,>}, val=> ]
*/
        static function q_where($args)
        {

        }

        static function q_delete($table,$where)
        {
            if(is_string($where))  
            {

            }
            else   
            { 
                $where=self::q_where($where);
            }
            return "DELETE FROM @+{$table} WHERE $where";
        }
    }
}
