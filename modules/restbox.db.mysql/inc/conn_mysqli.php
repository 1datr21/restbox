<?php
namespace modules\restbox\db\mysql {
    use Core;
    use Exception;
    use modules\restbox\db\RBDBConnection as RBDBConnection;
    use modules\restbox\RBModule as RBModule;
    
    class MySQLiConnection extends RBDBConnection {


        function get_err_mess()
		{
            return mysqli_connect_error();
        }
        
        function get_err_no()
		{
            return mysqli_connect_errno();
        }

        function make_connection($_dbcfg)
        {
            return new \mysqli($_dbcfg['host'],$_dbcfg['user'],$_dbcfg['passw'],$_dbcfg['dbname']);

        }   
        
        function get_result_count($result)
		{
            return $result->num_rows;
		}


        function fetch_object($res)
		{
			return mysqli_fetch_assoc($res);
		}
        
        function exec_query($_query)
        {
            //print_dbg($_query);
            $res = $this->_CONNECTION->query($_query);
            //print_dbg($res);  
             return $res;
 		}
        
      /*  function create_db($conn,$params)
		{
			$_query = "CREATE DATABASE {$params['dbname']} CHARACTER SET {$params['charset']} COLLATE {$params['collation']} ";
		//	mul_dbg($_query);
			$conn->query($_query);
		}*/
    }

}