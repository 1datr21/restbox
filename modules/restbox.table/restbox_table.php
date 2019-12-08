<?php
namespace modules\restbox\table {
	use Core;
	use Core\Router as Router;

	require_once '/inc/ft_basic.php';
	require_once '/inc/ft_id.php';
	require_once '/inc/ft_text.php';
	require_once '/inc/obj_table.php';

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
			$router = new Router('tables/:table:/[:id:]');
			$_query = $router->match($eargs['route']);
			if($_query!==false)
			{
				$_o_key = ObjTable::getKey($_query);
				print_dbg("key : ".$_o_key);
				$this->exe_mod_func('get_or_add_obj',$_o_key)	
			}
			//print_dbg($_res);

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