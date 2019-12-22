<?php
namespace modules\restbox\db\mysql {
    use Core;
    use Exception;
    use modules\restbox\db\RBDBConnection as RBDBConnection;
    use modules\restbox\RBModule as RBModule;
    
    class PDOConnection extends RBDBConnection {


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
         //   new PDO("mysql:host=$host;dbname=$dbname", $_dbcfg['user'],$_dbcfg['passw']);
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
        
        function last_insert_id()
		{
			return mysqli_insert_id($this->_CONNECTION);
		}
        
        function exec_query($_query)
        {
            $res = $this->_CONNECTION->query($_query);
            return $res;
 		}
   
    }

}