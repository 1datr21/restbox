<?php

namespace modules\restbox {
	require_once '/appobj.php';
	require_once '/obj_description.php';
    use Core;
    
    class RBModule extends \Core\Module {

        function call_obj($_route,$obj_class,$rmode=null)
		{
			$ptrn_list = call_user_func($obj_class .'::GetRoutePatterns',$rmode);

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

				print_dbg($_obj);
				$res_obj = $_obj->ExeAction($_request['action'],$_request['request']);
				return $res_obj;
			}
			return null;
		}
		

    }

}