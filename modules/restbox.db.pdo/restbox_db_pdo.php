<?php


namespace modules\restbox\db\pdo {

	require_once '/inc/conn_pdo.php';
	
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
			return ['pdo'=>'modules\restbox\db\mysql\PDOConnection'];
		}	

		

		
	}

}