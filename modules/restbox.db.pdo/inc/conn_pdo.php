<?php
namespace modules\restbox\db\mysql {
    use Core;
    use Exception;
    use modules\restbox\db\RBDBConnection as RBDBConnection;
    use modules\restbox\RBModule as RBModule;
    
    class PDOConnection extends RBDBConnection {

        VAR $_curr_ERROR=false;

   /*     function __construct($_params, $p_module=null)
        {
			def_options([
                'create_if_not_exists'=>false,
                'prefix' =>"",
				'conn_opts'=>[
                    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES   => false,
                ],

			],$_params);
			$this->_CONFIG = $_params;  
			$this->P_MODULE = $p_module; 
			$this->_CONNECTED = $this->connect($_params);			
			
        }*/
        
        function OnConstruct(&$_params)
        {
            def_options([
				'create_if_not_exists'=>false,
                'ENGINE'=>'InnoDB',
                'prefix' =>"",
                'conn_opts'=>[
                    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES   => false,
                ],

			],$_params);
        }

        
        function hasError()
		{
            return $this->_curr_ERROR;
        }

        function make_connection($_dbcfg)
        {
            $this->_curr_ERROR = false;
            try {
                $conn = new \PDO($_dbcfg['connstr'],$_dbcfg['user'],$_dbcfg['passw'],$_dbcfg['conn_opts']);
                $this->_CONNECTED =true;
                $this->_CONNECTION = $conn;
                return $conn;
            }
            catch(Exception $exc)
            {
              //  print_dbg("eeerrrrorr:".$exc->getMessage());
                $this->_curr_ERROR = true;
                $this->gen_error("Connection failed ".$exc->getMessage());
                
            //    print_dbg("@===");
            //    print_dbg($this->_ERRORS);

                $this->_CONNECTED =false;
                return null;
            }
            return null;

        }   

        function gen_error($mes=null,$err_no=null)
		{            
            $this->_ERRORS[]=['message'=>$mes,'errno'=>$err_no];
        //    print_dbg($this->_ERRORS);
		}
        
        function get_result_count($result)
		{
            return $result->rowCount();
		}


        function fetch_object($res)
		{
			return $res->fetch(); // \PDO::FETCH_LAZY
        }
        
        function last_insert_id()
		{
			return mysqli_insert_id($this->_CONNECTION);
		}
        
        function exec_query($_query,$gen_error=true)
        {
            try {
                $res = $this->_CONNECTION->query($_query);
                return $res;
            }
            catch(Exception $exc){
                if($gen_error)
                {                   
                    $this->gen_error("Error on query. ".$exc->getMessage());
                    print_dbg("<< error in sql \n\r{$_query}");
                    return false;
                }
            }
            return null;
 		}
   
    }

}