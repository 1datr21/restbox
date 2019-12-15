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
		VAR $_CONNECTION;
		VAR $_ERRORS;

        function __construct($_params)
        {
			def_options(['create_if_not_exists'=>false],$_params);
			$this->_CONFIG = $_params;   
			$this->_CONNECTED = $this->connect($_params);			
			
        }

		function restbox_db_get_db_drivers()
		{
			return [];
		}	

		function isConnected()
		{
			return $this->_CONNECTED;
		}
		
		function get_err_mess()
		{
          
        }

		public function connect($_dbcfg)
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
			try
			{
				def_options(['create_if_not_exists'=>false],$_dbcfg);
				if($_dbcfg['create_if_not_exists'])
				{
					$this->_CONNECTION = $this->make_connection($_dbcfg);// new \mysqli($_dbcfg['host'],$_dbcfg['user'],$_dbcfg['passw']);
					if(!$this->_CONNECTION->select_db($_dbcfg['dbname']))
					{
						$this->create_db($this->_CONNECTION,$_dbcfg);
					}
					$this->_CONNECTION->select_db($_dbcfg['dbname']);
					if(mysqli_connect_errno())
					{
						$this->gen_error(); 
						return false;
					}
				}
				else
				{
					$this->_CONNECTION = $this->make_connection($_dbcfg);// 
					if(mysqli_connect_errno())
					{
						$this->gen_error();
						return false;
					}
				}
			}
			catch(Exception $ex) {
				return false;
			}
			error_reporting(E_ALL);  //
			return true;
		}

		function getError($err_idx=null)
		{
			if($err_idx==null)
				$err_idx=count($this->_ERRORS)-1;
			return $this->_ERRORS[$err_idx];
		}

		function gen_error()
		{
			$this->_ERRORS[]=['message'=>"Connection failed ". $this->get_err_mess(),'errno'=>$this->get_err_no()];
		}

		function get_err_no()
		{
            
		}
		
		function fetch_object($res){

		}
		
        function query($_query_args)
        {
            $prepared = $this->prepare_query($_query_args);
            return $this->exec_query($prepared);
		}

		function build_query($qargs)
		{

		}
		
		function make_connection($_settings)
		{

		}

        function prepare_query($sql)
        {
		//	print_dbg('_CONFIG:');
		//	print_dbg($this->_CONFIG);
            return strtr($sql,['@+'=>$this->_CONFIG['prefix']]);
        }

        function exec_query($_query)
        {

		}
		
		function create_db($dbname,$_settings)
		{

		}

		function select_db($dbname)
		{

		}
    }
}