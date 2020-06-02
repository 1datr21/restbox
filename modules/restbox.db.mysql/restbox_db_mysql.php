<?php


namespace modules\restbox\db\mysql {

	require_once 'inc/conn_mysqli.php';
	
	use Core;
	use Exception;
	use modules\restbox\RBModule as RBModule;
	use modules\restbox\db\RBDBDriver as RBDBDriver;

	class Module  extends RBDBDriver // RBModule 
	{
		VAR $_CONF;
		VAR $_EP;
		VAR $_CONFIGS_AREA;
		VAR $_NO_READ_CONFIG;
		VAR $_CURR_CONF_DIR;
		VAR $_CONF_PATH;
		VAR $_SETTINGS;
		VAR $_CONNECTION;
		
		function __construct($_PARAMS)
		{
			
		}
		
		function restbox_db_get_db_drivers()
		{
			return ['mysql'=>'modules\restbox\db\mysql\MySQLiConnection'];
		}	

		

		
	}

}