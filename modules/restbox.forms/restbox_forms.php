<?php
namespace modules\restbox\forms {
	use Core;
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
		VAR $_OBJ_BUF;
		
		function __construct($_PARAMS)
		{
			//echo "THIS IS RESTBOX";
			parent::__construct($_PARAMS);	
			
		}
		
		
			
		function AfterLoad()
		{
			//	
		}

	
	}

}
