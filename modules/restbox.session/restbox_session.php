<?php

namespace modules\restbox\session {
	use Core;
	use Core\Router as Router;
	use modules\restbox\AppObject;
	use modules\restbox\RBModule as RBModule;

	require_once '/inc/authtable.php';

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
		
		}

		function restbox_route_onquery(&$eargs)
		{				
			$obj_res = $this->call_obj($eargs['route'],'modules\restbox\session\ObjAuthTable');
			
			return $obj_res;
		}

		function gen_token()
		{
			$this->sess_id = GenRandStr(10);
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
