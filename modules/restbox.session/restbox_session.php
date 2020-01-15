<?php

namespace modules\restbox\session {
	use Core;
	use Core\Router as Router;
	use modules\restbox\AppObject;
	use modules\restbox\RBModule as RBModule;

	require_once '/inc/obj_authtable.php';

	class Module extends RBModule 
	{
		VAR $_CONF;
		VAR $_EP;
		VAR $_CONFIGS_AREA;
		VAR $_NO_READ_CONFIG;
		VAR $_CURR_CONF_DIR;
		VAR $_CONF_PATH;
		VAR $_SETTINGS;
		VAR $_F_TYPES;
		VAR $sess_id;
		VAR $_SESS_INFO;
		
		function __construct($_PARAMS)
		{
		//	print_dbg(">> +++ ",true,true);
		}

		function AfterLoad()
		{
		//	print_dbg($_SERVER);
		}

		public function get_rb_token()
		{
			if(!empty(rtrim(ltrim($_SERVER['HTTP_RBTOKEN']))))
			{
				if($_SERVER['HTTP_RBTOKEN']=="null")
					return null;
				return $_SERVER['HTTP_RBTOKEN'];
			}
			return null;
		}

		function restbox_route_onquery(&$eargs)
		{				
			$obj_res = $this->call_obj($eargs['route'],'modules\restbox\session\ObjAuthTable');
			
			return $obj_res;
		}

		function gen_token()
		{
			$this->sess_id = GenRandStr(25);
		}

		function find_this_token($token_str)
		{
			$handled = false;
			$token_res = null;
			$this->call_event('session_find',['token'=>$token_str,],['onhandle'=>function($modname,$ev_res,&$_continue) use ($handled,$token_res)
			{
				$handled=true;
				$token_res = $ev_res;
			}]);
			if(!$handled)
			{
				return $this->_std_find_by_token($token_res);
			}
			else
			{
				return $token_res;
			}
		}

		function _std_find_by_token($tkn_str)
		{
			$sess_file_name = "/sessions/{$tkn_str}.sess";
			return file_exists($sess_file_name);
		}

		function start_session()
		{
			$this->gen_token();
			return $this->sess_id ;
		}

		function get_session_vars()
		{
			return $this->_SESS_INFO;
		}

		function change_token()
		{

		}

		function sess_expired()
		{
			//filemtime()
		}

		function save_session()
		{
			$sess_file_name = $this->sess_id.".sess";
			file_put_contents($sess_file_name,serialize($this->_SESS_INFO));
		}
		
	}

	

}
