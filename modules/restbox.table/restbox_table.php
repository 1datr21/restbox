<?php
namespace modules\restbox\table {
	use Core;
	use Core\Router as Router;

	require_once '/inc/ft_basic.php';
	require_once '/inc/ft_id.php';
	require_once '/inc/ft_text.php';

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

/*
		tables/:table:/[:id:]	
		tables/table:users/id:1
*/
		
		function restbox_route_onquery(&$eargs)
		{
			//print_dbg($eargs);
			$router = new Router('tables/one/:table:/[:id:]');
			$router->match($eargs['route']);
			//$this->read_route($eargs['route'],'tables/:table:/[:id:]');

			return [
				'message'=>'Hello',
				'date'=>time(),
				'num'=>rand(0,100),		
			];
		}	
	}

	class tfield {
		function __construct($_class,$_PARAMS=[])
		{
			
		}
	}

}