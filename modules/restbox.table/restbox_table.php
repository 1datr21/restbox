<?php


namespace modules\restbox\table {
	use Core;
	use Core\Router as Router;
	use modules\restbox\AppObject;
	use modules\restbox\RBModule as RBModule;

	require_once '/inc/ftypes/ft_basic.php';
	require_once '/inc/ftypes/ft_id.php';
	require_once '/inc/ftypes/ft_text.php';
	require_once '/inc/ftypes/ft_password.php';
	require_once '/inc/obj_table.php';

	use modules\restbox\table\ft_id as ft_id;
	

	class Module extends RBModule 
	{
		VAR $_CONF;
		VAR $_EP;
		VAR $_CONFIGS_AREA;
		VAR $_NO_READ_CONFIG;
		VAR $_CURR_CONF_DIR;
		VAR $_CONF_PATH;
		VAR $_SETTINGS;
		VAR $_F_TYPES;
		
		function __construct($_PARAMS)
		{
		//	print_dbg(">> +++ ",true,true);
		}

/*
		tables/:table:/[:id:]	
		tables/table:users/id:1
*/
		
		function restbox_route_onquery(&$eargs)
		{		
			$this->gather_ftypes();
			$obj_res = $this->call_obj($eargs['route'],'modules\restbox\table\ObjTable');
			
			return $obj_res;
		}	

		function gather_ftypes()
		{
		//	print_dbg($this->MLAM->module_list(),true,true);
		}
	}

	class tfield {
		VAR $field_info;
		function __construct($_class,$_PARAMS=[])
		{

			if(strpos($_class,'\\')===false)
			{
				$_classname = 'modules\restbox\table\ft_'.$_class;
			}
			else
			{
				$_classname = $_class;
			}
			$this->field_info = new $_classname($_PARAMS);
		}
	}

}