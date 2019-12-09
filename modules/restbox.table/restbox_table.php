<?php
namespace modules\restbox\table {
	use Core;
	use Core\Router as Router;
    use modules\restbox\AppObject;

require_once '/inc/ftypes/ft_basic.php';
	require_once '/inc/ftypes/ft_id.php';
	require_once '/inc/ftypes/ft_text.php';
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
		//	ObjTable::FindPattern($eargs['route']);
			$router = new Router('tables/:table:/[:id:]');
			$_query = $router->match($eargs['route']);
			$res_obj = [];
			if($_query!==false)
			{
				$_o_key = ObjTable::getKey($_query);
				if($this->exe_mod_func('restbox.route','obj_exists',$_o_key))
				{
					$_obj = $this->exe_mod_func('restbox.route','get_obj', $_o_key);
				}
				else
				{
					$_cfg_info = $this->exe_mod_func('restbox', 'get_settings');
					$_obj = $this->exe_mod_func('restbox.route','add_obj', new ObjTable($_query, $_cfg_info), $_o_key);
				}

				$res_obj = $_obj->ExeAction($_query);
			}
			return $res_obj;
		}	
	}

	class tfield {
		function __construct($_class,$_PARAMS=[])
		{
			
		}
	}

}