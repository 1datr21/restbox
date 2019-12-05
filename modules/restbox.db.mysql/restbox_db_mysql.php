<?php
namespace modules\restbox\db\mysql {
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
		VAR $_CONNECTION;
		
		function __construct($_PARAMS)
		{
			
		}
		
		private function connect($_dbcfg)
		{
			$_CONNECTION = new mysqli($_dbcfg['host'],$_dbcfg['user'],$_dbcfg['passw']);
		}

		function create_table()
		{

		}

		function query($_query)
		{
			mysqli_query($this->_CONNECTION,$_query);
		}

		function fetch_object($res)
		{
			return mysqli_fetch_assoc($res);
		}
	}

}