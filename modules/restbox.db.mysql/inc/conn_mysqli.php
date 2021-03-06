<?php
namespace modules\restbox\db\mysql {
    use Core;
    use Exception;
    use modules\restbox\db\RBDBConnection as RBDBConnection;
    use modules\restbox\RBModule as RBModule;
    
    class MySQLiConnection extends RBDBConnection {

        function OnConstruct(&$_params)
        {
            def_options([
				'create_if_not_exists'=>false,
				'ENGINE'=>'InnoDB',

			],$_params);
        }

        function get_err_mess()
		{
            return mysqli_connect_error();
        }
        
        function get_err_no()
		{
            return mysqli_connect_errno();
        }

        function hasError()
		{
			return mysqli_connect_errno();
		}

        function make_connection($_dbcfg)
        {
            $conn = new \mysqli($_dbcfg['host'],$_dbcfg['user'],$_dbcfg['passw'],$_dbcfg['dbname']);
            $this->_CONNECTED = !$this->hasError();
            //print_dbg($conn);
            return $conn;
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

        
        function exec_query($_query,$gen_error=true)
        {
            // print_dbg($_query);
            $res = $this->_CONNECTION->query($_query);
            return $res;
 		}
   
    }

}