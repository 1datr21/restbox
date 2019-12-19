<?php


namespace modules\restbox\table {
	use Core;
	use Core\Router as Router;
	use modules\restbox\AppObject;
	use modules\restbox\RBModule as RBModule;

	require_once '/inc/ftypes/ft_basic.php';
	require_once '/inc/obj_table.php';
/*	
	require_once '/inc/ftypes/ft_id.php';
	require_once '/inc/ftypes/ft_text.php';
	require_once '/inc/ftypes/ft_password.php';
	

	use modules\restbox\table\ft_id as ft_id;
*/	

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
		function AfterLoad()
		{
		
		}

		function restbox_route_onquery(&$eargs)
		{				
			$obj_res = $this->call_obj($eargs['route'],'modules\restbox\table\ObjTable');
			
			return $obj_res;
		}	

		function gather_ftypes()
		{
		//	print_dbg($this->MLAM->module_list(),true,true);
			if(!empty($this->_F_TYPES)) 
				return;
			$ns_def = 'modules\\restbox\\table\\';
			$F_TYPES = [
					'id'=>['file'=>'/inc/ftypes/ft_id.php','ns'=>$ns_def,'class'=>'ft_id'],
					'text'=>['file'=>'/inc/ftypes/ft_text.php','ns'=>$ns_def,'class'=>'ft_text'],
					'bigtext'=>['file'=>'/inc/ftypes/ft_bigtext.php','ns'=>$ns_def,'class'=>'ft_bigtext'],
					'password'=>['file'=>'/inc/ftypes/ft_password.php','ns'=>$ns_def,'class'=>'ft_password']
				];
			$params=[];
			$obj = $this;
			$this->call_event('get_f_types',$params,['onhandle'=>function($modname,$ev_res,&$_continue) use (&$F_TYPES)
				{
					foreach($ev_res as $ftname => $eritem)
					{
						if(substr($eritem['ns'],-strlen($eritem['ns'])+1)=='//')
							$eritem['ns']=$eritem['ns']."\\";
						
						$eritem['file']= url_seg_add("./modules/$modname",$eritem['file']);
						$F_TYPES[$modname.".".$ftname]=$eritem;
					}
				}
			]);

			$this->_F_TYPES = $F_TYPES;
			//print_dbg($this->_F_TYPES);		
		}

		function load_ftype($ftname,$ftparams)
		{			
			$this->gather_ftypes();	
			//print_dbg($ftparams);

			if(isset($this->_F_TYPES[$ftparams->_ftype]))
			{
				//print_dbg($this->_F_TYPES[$ftparams->_ftype]);

				require_once $this->_F_TYPES[$ftparams->_ftype]['file'];

				$ftclass = strtr(url_seg_add($this->_F_TYPES[$ftparams->_ftype]['ns'],$this->_F_TYPES[$ftparams->_ftype]['class']),['/'=>'\\']);

				return new $ftclass($ftparams->_info,$ftname);

			}
			return null;
		}
	}

	class tfield {
		VAR $_info;
		VAR $_ftype;

		function __construct($_ftype,$_PARAMS=[])
		{			
			$this->_info = $_PARAMS;
			$this->_ftype = $_ftype;
		}
	}

}