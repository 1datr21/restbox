<?php
namespace modules\restbox\db {
	use Core;
	use modules\restbox\RBModule as RBModule;
	require_once '/inc/rbdbdriver.php';

	class Module extends RBModule 
	{
		VAR $_CONF;
		VAR $_EP;
		VAR $_CONNECTIONS;
		VAR $_DEF_CONN_IDX;
		
		function __construct($_PARAMS)
		{
			
		}


		public function query_select($_params,$conn_id=0)
		{
			return $this->_CONNECTIONS[$conn_id]->query_select($_params);
		}

		public function query($qargs,$params=[],$conn_id=0)
		{
			if(!isset($this->_CONNECTIONS[$conn_id]))
			{
				$this->exe_mod_func('restbox','out_error',['mess'=>'Connection not exists']);
			}
			return ['qres'=>$this->_CONNECTIONS[$conn_id]->query($qargs) ,'conn_id'=>$conn_id ];
		}	

		public function fetch_object($res)
		{
			return $this->_CONNECTIONS[$res['conn_id']]->fetch_object($res['qres']);
		}

		function query_text_select($_params)
		{

		}

		public function connect($conn_info,$conn_id=0)
		{			
			change_key('drv','driver',$conn_info);
			//print_dbg($conn_info);
			$_drv_class = null;
			$opts=['onhandle'=>function($modname,$ev_res,&$_continue) use (&$conn_info,&$_drv_class)
			{
				if( isset($ev_res[$conn_info['driver']] ))
				{
					$_continue = false;
					$_drv_class = $ev_res[$conn_info['driver']];					
				}
			}];
			$_json_res=[];
			$args=['conn_info'=>$conn_info,];
			$query_res = $this->call_event('get_db_drivers',$args,$opts);

			//try to connect it
			$db_conn = new $_drv_class($conn_info,$this);
			if($db_conn->isConnected())
			{
				$this->_CONNECTIONS[$conn_id] = $db_conn;
			}
			else
			{
				$this->exe_mod_func('restbox','out_error',$db_conn->getError());
			}
			//print_dbg($this->_CONNECTIONS);
		}

		public function connection_exists($conn_id)
		{
			
			return isset($this->_CONNECTIONS[$conn_id]);
		}
			
	}

}