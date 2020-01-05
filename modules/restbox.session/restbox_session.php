<?php

namespace modules\restbox\session {
	use Core;
	use Core\Router as Router;
	use modules\restbox\AppObject;
	use modules\restbox\RBModule as RBModule;

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
		
		function __construct($_PARAMS)
		{
		//	print_dbg(">> +++ ",true,true);
		}

		function AfterLoad()
		{
		
		}

		function gen_token()
		{
			$sess_id = GenRandStr(10);
		}

		function save_session()
		{
			
		}
		
	}

	

}
