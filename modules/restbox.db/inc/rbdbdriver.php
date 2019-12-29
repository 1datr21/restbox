<?php

namespace modules\restbox\db {
	use Core;
    use Exception;
	use modules\restbox\RBModule as RBModule;
	
	require_once '/sqlbuilder.php';

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
			$this->OnConstruct($_params);
			$this->_CONFIG = $_params;  
			$this->P_MODULE = $p_module; 
			$this->_CONNECTED = $this->connect($_params);			
			
		}

		function OnConstruct(&$_params)
        {
           
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
			$this->dispatch_table($table_map);

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

		// dispatch table fields
		function dispatch_table($table_map)
		{
			if( $this->_CONFIG['create_if_not_exists'])
			{
				if( !$this->table_exists($table_map->getName()) )
				{
				//	print_dbg('table not exists');
					$this->create_table($table_map);
				}
				else
				{
					$need_fields = $table_map->get_need_fields();

					$existing_fields = $this->get_fields($table_map->getName());
					// remove columns
					$flds=[];
					foreach($existing_fields as $ex_fld)
					{
						if(!in_array( $ex_fld['Field'],$need_fields))
						{
							$this->delete_field($table_map->getName(),$ex_fld['Field']);// OnDropField()
						}
						else
						{
							$flds[]=$ex_fld;
						}
					}
					$existing_fields = $flds;
					
					$exst_field_list = assoc_array_cut($existing_fields,"Field");
				
				//	print_dbg($exst_field_list);
					// add not existing fields
					$fld_prev = null;
					foreach($table_map->FIELDS as $fld => $finfo) 
					{
						$need_for_fld = $finfo->get_fields();						

						$must_add = false;
						foreach($need_for_fld as $__fld)
						{
							if(!in_array($__fld,$exst_field_list))
							{
								$must_add = true;
							}
						}

						if($must_add)
							$this->add_column($finfo,$table_map,$fld_prev);
						
						$fld_prev = $fld;
					}
								
			//print_dbg($sql);
		//	$this->query($sql);

			
				}
			}
		}

		function last_insert_id()
		{
			
		}

		function change_column($finfo,$table_map,$errshow=false)
		{
/* ALTER TABLE `tms_users`
	CHANGE COLUMN `avatar_mime` `avatar_mime` VARCHAR(50) NOT NULL AFTER `avatar`;  */
			$_args=['table'=>$table_map->getName()];
			$res = null;

			$args = ['table'=>$table_map->getName(),'finfo'=>$finfo,'driver'=>$this];
			$opts=['onhandle'=>function($modname,$ev_res,&$_continue) use (&$res)
			{
				$res = $ev_res;
				$_continue = false;
				
			}];
			$_json_res=[];
			$query_res = $this->P_MODULE->call_event('OnChangeFld_std',$args,$opts);

			// create if standart
			if($res===null)
				$res = $finfo->OnChangeFld_std($_args);

			$q_ext=[];
			if(!empty($res['add_queries']))
			{
				foreach($res['add_queries'] as $q)
				{
					$q_ext[]=$q;
				}
			}

			$_str = "ALTER TABLE `@+{$table_map->getName()}` ADD COLUMN  {$res['fld_seg']} AFTER `{$fld_prev}`";
			//print_dbg($_str);
			$this->query($_str,$errshow);

			foreach($q_ext as $query)
			{
				$query=strtr($query,['[table]'=>$table_map->getName()]);
				//	print_dbg($query);
				$this->query($query);
			}
		}

		function add_column($finfo,$table_map,$fld_prev=null,$errshow=false)
		{	
			$_args=['table'=>$table_map->getName()];
			$res = null;

			$args = ['table'=>$table_map->getName(),'finfo'=>$finfo,'driver'=>$this];
			$opts=['onhandle'=>function($modname,$ev_res,&$_continue) use (&$res)
			{
				$res = $ev_res;
				$_continue = false;
				
			}];
			$_json_res=[];
			$query_res = $this->P_MODULE->call_event('OnCreateNewFld_std',$args,$opts);

			// create if standart
			if($res===null)
				$res = $finfo->OnCreateNewFld_std($_args);

			$q_ext=[];
			if(!empty($res['add_queries']))
			{
				foreach($res['add_queries'] as $q)
				{
					$q_ext[]=$q;
				}
			}

			$_str = "ALTER TABLE `@+{$table_map->getName()}` ADD COLUMN  {$res['fld_seg']} AFTER `{$fld_prev}`";
			//print_dbg($_str);
			$this->query($_str,$errshow);

			foreach($q_ext as $query)
			{
				$query=strtr($query,['[table]'=>$table_map->getName()]);
				//	print_dbg($query);
				$this->query($query);
			}
		}

		function query_insert($args)
		{
			$table = $args['table'];
			$item = $args['item'];
			$_sql = "INSERT INTO `@+$table`(".xx_implode($item,',',"`{idx}`").") VALUES(".xx_implode($item,',','\'{%val}\'',function(&$theval,&$idx,&$thetemplate,&$ctr,$thedelimeter){
			//	print_dbg($theval);
				if(substr($theval['%val'],0,1)=='#')
				{
					$thetemplate = "{%val}";
				}
			}).")";

		//	print_dbg($_sql);
			$this->query($_sql);
			/*
			INSERT INTO `tmsus`.`tms_users` (`login`, `email`) VALUES ('vasyan', 'thev@ya.ru');
			*/
		}

		function query_update($table,$item,$idval,$idfld='id')
		{
			$_sql = "UPDATE `@+{$table}` SET ".xx_implode($item,',',"`{idx}`='{%val}'")." WHERE  `{$idfld}`={$idval}";
			$this->query($_sql);	
			/*
			UPDATE `tmsus`.`tms_users` SET `passw`='122' WHERE  `id`=1;
			*/
		}

		function delete_field($table,$fld)
		{
			$this->query("ALTER TABLE `@+$table` DROP COLUMN `$fld`");	
		}

		function get_fields($_table)
		{
			$res = $this->query("DESCRIBE `@+{$_table}`");
			$rows=[];
			while($row=$this->fetch_object($res))
			{
				$rows[]=$row;
			}
			return $rows;
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
				$query_res = $this->P_MODULE->call_event('OnCreateTable_std',$args,$opts);

				// create if standart
				if($res===null)
					$res = $finfo->OnCreateTable_std($_args);
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
				$this->query($query, false);
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
					if($this->isConnected())
					{
						print_dbg("connected @");
						return true;
					}
					
				//	print_dbg('not connected');
					if($this->hasError())
					{
					//	$this->gen_error(); 
						
						return false;
					}
					if(!$this->select_db($_dbcfg['dbname']))
					{
						//print_dbg('create DB');
						$cfg_without_name = $_dbcfg;
						unset($cfg_without_name['dbname']);

						$this->_CONNECTION = $this->make_connection($cfg_without_name);// 
						$this->create_db($this->_CONNECTION,$_dbcfg);

						$this->_CONNECTION = $this->make_connection($_dbcfg);// connect existing db
					}
					$this->_CONNECTION->select_db($_dbcfg['dbname']);
					if($this->hasError())
					{
					//	$this->gen_error(); 
						return false;
					}
				}
				else
				{
					$this->_CONNECTION = $this->make_connection($_dbcfg);// 
					if($this->hasError())
					{
					//	$this->gen_error();
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

		function hasError()
		{
			return false;
		}

		function getError($err_idx=null)
		{
			if($err_idx==null)
				$err_idx=count($this->_ERRORS)-1;
			return $this->_ERRORS[$err_idx];
		}

		function gen_error($mess=null)//
		{
			if($mess==null) 
				$mess = $this->get_err_mess();
			$this->_ERRORS[]=['message'=>$mess,'errno'=>$this->get_err_no()];
		}

		function get_err_no()
		{
            
		}
		
		function fetch_object($res){

		}
		
        function query($_query_args,$show_error=true)
        {
		//	print_dbg($_query_args);
            $prepared = $this->prepare_query($_query_args);
			$e_res = $this->exec_query($prepared,$show_error);
			//print_dbg($e_res);
			if($show_error)
			{
				if($e_res===false)
				{
				//	$this->P_MODULE->gen_error($this->getError());
					$this->P_MODULE->exe_mod_func('restbox','out_error',$this->getError());
				}
			}
			return $e_res;
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

        function exec_query($_query,$gen_error=true)
        {

		}
		
		

		function select_db($dbname)
		{

		}
    }
}