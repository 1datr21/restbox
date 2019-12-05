<?php
namespace modules\restbox\db {
	use Core;

	class Module extends \Core\Module
	{
		VAR $_CONF;
		VAR $_EP;
		VAR $_CONFIGS_AREA;
		VAR $_NO_READ_CONFIG;
		VAR $_CURR_CONF_DIR;
		VAR $_CONF_PATH;
		VAR $_SETTINGS;
		
		function __construct($_PARAMS)
		{
			
		}
		
		function restbox_after_load_config($args)
		{
			$rb_info = $this->exe_mod_func('restbox','get_settings');
			
		}
	}

}