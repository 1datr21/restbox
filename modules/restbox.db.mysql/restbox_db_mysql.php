<?php
namespace modules\restbox\db\mysql {
	use Core;
    use Exception;

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
		
		public function connect($_dbcfg)
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
			try
			{
				def_options(['create_if_not_exists'=>false],$_dbcfg);
				if($_dbcfg['create_if_not_exists'])
				{
					$_CONNECTION = new \mysqli($_dbcfg['host'],$_dbcfg['user'],$_dbcfg['passw']);
				}
				else
				{
					$_CONNECTION = new \mysqli($_dbcfg['host'],$_dbcfg['user'],$_dbcfg['passw'],$_dbcfg['dbname']);
					if(mysqli_connect_errno())
					{
						return ['error'=>"Connect failed: %s\n". mysqli_connect_error()];
					}
				}
			}
			catch(Exception $ex) {}
			error_reporting(E_ALL);
			return $_CONNECTION;
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