<?php

namespace modules\restbox\db {
	use Core;
	use modules\restbox\RBModule as RBModule;

	class RBDBDriver extends RBModule 
    {
        function query_select($_params)
		{
			def_options([
				'page_size'=>20,
				'use_page'=>true,
                'chunk_by'=>0,
                'page'=>1,
			],$_params);
			if($_params['use_page'])
			{
                $q_total = "SELECT COUNT(*) as t_count FROM @+".$_params['table']."";
                
                
                $l_0 = $_params['page_size']*($_params['page']-1);
                $q_page = "SELECT COUNT(*) as t_count FROM @+".$_params['table']." LIMIT {$l_0 },{$_params['page_size']}";
			}
			else
			{

			}
			$query = "";
		}
		
		public function get_conn_class_name()
		{
			
		}

        public function connect($_dbcfg)
        {

        }

        function exe_query($_prepared_query)
        {

        }
        
        function prepare_query($q_text,$_params)
        {

        }
    }

    class RBDBConnection {

		VAR $_CONFIG;
		VAR $_CONNECTED;

        function __construct($_params)
        {
			def_options(['create_if_not_exists'=>false],$_params);
			$this->_CONFIG = $_params;   
			$this->_CONNECTED = $this->connect($_params);
        }

        public function connect($_dbcfg)
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
			try
			{
				
				if($_dbcfg['create_if_not_exists'])
				{
					$_CONNECTION = new \mysqli($_dbcfg['host'],$_dbcfg['user'],$_dbcfg['passw']);
					if(!$_CONNECTION->select_db($_dbcfg['dbname']))
					{
						$this->create_db($_CONNECTION,$_dbcfg);
					}

					$_CONNECTION->select_db($_dbcfg['dbname']);

					if(mysqli_connect_errno())
					{
						return ['error'=>"Connect failed: %s\n". mysqli_connect_error()];
					}
				}
				else
				{
					$_CONNECTION = new \mysqli($_dbcfg['host'],$_dbcfg['user'],$_dbcfg['passw'],$_dbcfg['dbname']);
					if(mysqli_connect_errno())
					{
						return ['error'=>"Connect failed: %s\n". mysqli_connect_error()];
					}
				}
			}
			catch(Exception $ex) {}
			error_reporting(E_ALL);  //
			return $_CONNECTION;
        }
        
        function query($_query_args)
        {
            $prepared = $this->prepare_query($_query_args);
        }

        function prepare_query($sql)
        {
            return strtr($sql,['@+'=>$this->_CONFIG['prefix']]);
        }
    }
}