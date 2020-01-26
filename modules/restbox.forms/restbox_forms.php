<?php
namespace modules\restbox\forms {
	use Core;
	use modules\restbox\RBModule as RBModule;

	class Module extends RBModule 
	{
		VAR $_CONF;
		VAR $_EP;
		VAR $_CONFIGS_AREA;
		VAR $_NO_READ_CONFIG;
		VAR $_CURR_CONF_DIR;
		VAR $_CONF_PATH;
		VAR $_SETTINGS;
		VAR $_OBJ_BUF;
		
		function __construct($_PARAMS)
		{
			//echo "THIS IS RESTBOX";
			parent::__construct($_PARAMS);	
			
		}

		function call_obj($_route,$obj_class)   
		{
		//	print_dbg($_route);
			$map = [];
			$r_pieces = explode('/',$_route);
			if($r_pieces[0]=='forms')
			{
				$form_name = $r_pieces[1];
				array_unshift($r_pieces,0);
				array_unshift($r_pieces,1);
				print_dbg($r_pieces);
			}
		/*	$ptrn_list = call_user_func($obj_class .'::GetRoutePatterns');

			$_request = call_user_func($obj_class . '::FindPattern', $_route, $ptrn_list);
			if($_request!=false)
			{
				$_o_key = call_user_func($obj_class . '::getKey', $_request['request']);
				if($this->exe_mod_func('restbox.route','obj_exists',$_o_key))
				{
					$_obj = $this->exe_mod_func('restbox.route','get_obj', $_o_key);
				}
				else
				{
					$_cfg_info = $this->exe_mod_func('restbox', 'get_settings');
					$_obj = $this->exe_mod_func('restbox.route','add_obj', new $obj_class($_request['request'], $_cfg_info, $this), $_o_key);
				}

			//	print_dbg($_request);
				$res_obj = $_obj->ExeAction($_request['action'],$_request['request']);
				return $res_obj;
			}
			*/
			return null;
		}
		
		function restbox_route_onquery(&$eargs)
		{	
		//	print_dbg(":::");			
			$obj_res = $this->call_obj($eargs['route'],'modules\restbox\forms\ObjForm');
			
			return $obj_res;
		}	
			
		function AfterLoad()
		{
			//	
		}

	
	}

}
