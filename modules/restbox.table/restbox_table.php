<?php
namespace modules\restbox\table {
	use Core;
	use Core\Router as Router;
	use modules\restbox\AppObject;
	use modules\restbox\RBModule as RBModule;

require_once '/inc/ftypes/ft_basic.php';
	require_once '/inc/ftypes/ft_id.php';
	require_once '/inc/ftypes/ft_text.php';
	require_once '/inc/obj_table.php';
	

	class Module extends RBModule 
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
			$obj_res = $this->call_obj($eargs['route'],'modules\restbox\table\ObjTable');
		
			return $obj_res;
		}	
	}

	class tfield {
		function __construct($_class,$_PARAMS=[])
		{
			
		}
	}

}