<?php

namespace modules\restbox\db {
	use Core;
	use modules\restbox\RBModule as RBModule;

	class RBDBDriver extends RBModule 
    {
       
		
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
		VAR $P_MODULE;

        function __construct($_params, $p_module=null)
        {
			def_options([
				'create_if_not_exists'=>false,
				'ENGINE'=>'InnoDB',

			],$_params);
			$this->_CONFIG = $_params;  
			$this->P_MODULE = $p_module; 
			$this->_CONNECTED = $this->connect($_params);			
			
		}
		
		function query_select($_params)
		{
			def_options([
				'page_size'=>20,
				'use_page'=>true,
                'chunk_by'=>0,
				'page'=>1,
				'where'=>1,
			],$_params);

			$res_arr = [];
			$res_arr['items'] = [];

			$table_map = $_params['#table_params'];
			if( $this->_CONFIG['create_if_not_exists'])
			{
				if( !$this->table_exists($table_map->getName()) )
				{
					//print_dbg("TABLE ".$table_map->getName()." NOT EXISTS");
					$this->create_table($table_map);
				}
			}

			if($_params['use_page'])
			{
                $q_total = "SELECT COUNT(*) as t_count FROM `@+{$_params['table']}` WHERE {$_params['where']}";
				$res = $this->query($q_total);
				
				$res_row = $this->fetch_object($res);
				$res_arr['total_count'] = (int)$res_row['t_count'];
				$res_arr['page'] = $_params['page'];
				$res_arr['page_size'] = $_params['page_size'];
				
                
                $l_0 = $_params['page_size']*($_params['page']-1);
                $q_page = "SELECT * FROM `@+{$_params['table']}` WHERE {$_params['where']} LIMIT {$l_0 },{$_params['page_size']}";
				
			}
			else
			{
				$q_page = "SELECT * FROM `@+{$_params['table']}` WHERE {$_params['where']}";

			}

			$res = $this->query($q_page); // get the page items
			while($_row = $this->fetch_object($res))
			{
				$res_arr['items'][]=$_row;//
			}

			return $res_arr;
		}

		function create_db($_CONN, $_dbcfg)
		{
			$sql="CREATE DATABASE `{$_dbcfg['dbname']}` COLLATE '{$_dbcfg['collation']}' ";
			//print_dbg($sql);
			$this->query($sql);
		}

		function create_table($table_params)
		{
			//print_dbg($table_params);

			$sql="CREATE TABLE IF NOT EXISTS `@+{$table_params->getName()}` (";
			$i=0;
			$q_ext = [];
			foreach($table_params->FIELDS as $fld => $finfo) 
			{
				$_args=['table'=>$table_params->getName()];
				$res = null;
				
				$args = ['table'=>$table_params->getName(),'finfo'=>$finfo,'driver'=>$this];
				$opts=['onhandle'=>function($modname,$ev_res,&$_continue) use (&$res)
				{
					$res = $ev_res;
					$_continue = false;
					
				}];
				$_json_res=[];
				$query_res = $this->P_MODULE->call_event('onCreateTable',$args,$opts);

				// create if standart
				if($res===null)
					$res = $finfo->OnCreate_std($_args);
				if(!empty($res['fld_seg'] ))
				{
					if($i>0) 
						$sql = $sql .",". $res['fld_seg'] ;
					else  
						$sql = $sql .$res['fld_seg'] ;
					$i++;
				}
				if(!empty($res['add_queries']))
				{
					foreach($res['add_queries'] as $q)
					{
						$q_ext[]=$q;
					}
				}
			}
			$sql = $sql.") ENGINE={$this->_CONFIG['ENGINE']} DEFAULT CHARSET={$this->_CONFIG['charset']}";
			
			//print_dbg($sql);
			$this->query($sql);

			foreach($q_ext as $query)
			{
				$query=strtr($query,['[table]'=>$table_params->getName()]);
			//	print_dbg($query);
				$this->query($query);
			}
		}

		function get_one($params)
		{

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
		
		function table_exists($table)
		{
			$res = $this->query("SHOW TABLES LIKE '@+".$table."'");
			return ($this->get_result_count($res) > 0);

		}

		function get_result_count($res)
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
					$this->_CONNECTION = $this->make_connection($_dbcfg);// 
					if(!$this->_CONNECTION->select_db($_dbcfg['dbname']))
					{
						//print_dbg('create DB');
						$cfg_without_name = $_dbcfg;
						unset($cfg_without_name['dbname']);

						$this->_CONNECTION = $this->make_connection($cfg_without_name);// 
						$this->create_db($this->_CONNECTION,$_dbcfg);

						$this->_CONNECTION = $this->make_connection($_dbcfg);// connect existing db
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

		function build_query_select($qargs)
		{
			$res_sql = "SELECT * FROM {$qargs['table']}";
			return $res_sql;
		}
		
		function make_connection($_settings)
		{

		}

        function prepare_query($sql)
        {
            return strtr($sql,['@+'=>$this->_CONFIG['prefix']]);
        }

        function exec_query($_query)
        {

		}
		
		

		function select_db($dbname)
		{

		}
    }
}