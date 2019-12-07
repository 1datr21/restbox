<?php
namespace modules\restbox\table {
	use Core;
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
*/
		function read_route($route_str,$route_ptrn)
		{
			$this->route_ptr_map($route_ptrn);
		}

		function route_ptr_map($r_ptrn)
		{
			$res_map = [];
			$exploded = explode('/',$r_ptrn);
			foreach($exploded as $expl)
			{
				$map=[];
				preg_match_all("#\[(.+)\]#Uis",$expl,$map);
				if(count($map[0])==0)
				{
					$res_map[] = ['content'=>$expl,'type'=>'const'];	
				}
				else
				{
					print_dbg($map);	
					preg_match_all("#:(.+):#Uis",$expl,$map);
					//sscanf($expl,":%s:",$map);
				}
			}
		}

		function restbox_route_onquery(&$eargs)
		{
			//print_dbg($eargs);
			$this->read_route($eargs['route'],'tables/:table:/[:id:]');

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