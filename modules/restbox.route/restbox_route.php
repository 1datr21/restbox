<?php
namespace modules\restbox\route {
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
			//echo "THIS IS RESTBOX";
			parent::__construct($_PARAMS);
			$this->load_api();
			
		}
		
		function load_api()
		{

		}
			
		function AfterLoad()
		{
			//
	
		}
        
        function restbox_onload($args)
        {
            echo "<h2>..LOADING..</h2>";
        }
	}

}