<?php
namespace modules\restbox {
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
			$this->load_config();
			
		}
		
		function load_config()
		{
			include "./conf.php";
		}
			
		function AfterLoad()
		{
			//
			$args=[];
			$opts=[];
			$this->call_event('onload',$args,$opts);
		
			$res = $this->call_event('getresult',$args,$opts);
			$this->result_out($res);
		}

		function result_out($_res)
		{
			echo json_encode($_res);
		}
				
	}

}