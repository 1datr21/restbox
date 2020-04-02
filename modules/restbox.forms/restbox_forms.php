<?php
namespace modules\restbox\forms {
	use Core;
    use Exception;
	use modules\restbox\RBModule as RBModule;
	
	require_once '/inc/routing_obj.php';
	require_once '/inc/obj_form.php';

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
		VAR $_obj_map;
		
		function __construct($_PARAMS)
		{
			//echo "THIS IS RESTBOX";
			parent::__construct($_PARAMS);
			$this->_obj_map	= [
				'forms'=>'\modules\restbox\forms\ObjForm'
			];
		}

		function obj_info_by_route($_route)
		{
			$res=[];
			try{
				$r_pieces = explode('/',$_route);
				$res['obj_class'] = $r_pieces[0];
				if(!isset($this->_obj_map[$res['obj_class']]))
				{
					return null;
				}
				$res['object']=['name'=>$r_pieces[1],'type'=>'config'];
				$class_name = $this->_obj_map[$res['obj_class']];
				$res['object']['class']=$class_name;
				
				array_shift($r_pieces);
				array_shift($r_pieces);
				// get the action
				if(isset($r_pieces[0]))
				{
					$action_name = "A".ucfirst($r_pieces[0]);
				}
				
				if(!method_exists($class_name, $action_name))
				{
				//	print_dbg($class_name.'::GetDefAction');
					$action_name = call_user_func($class_name.'::GetDefAction');
				}
				else
				{
					array_shift($r_pieces);
				}
				$res['action']=$action_name;
				$res['route_pieces'] = $r_pieces;
			}
			catch(Exception $exc)
			{
				$this->exe_mod_func('restbox','out_error',['message'=>"URL parsing error",'errno'=>55]);	
			}
			return $res;
		}

		function call_routed_obj($_route)
		{
			$obj_nfo = $this->obj_info_by_route($_route);
			
			if(!isset($this->_obj_map[$obj_nfo['obj_class']]))
				return;
			$obj_class_name = $this->_obj_map[$obj_nfo['obj_class']];
			$obj = new $obj_class_name($obj_nfo,[],$this);
			$res = $obj->exe_action($obj_nfo['action'],$obj_nfo['route_pieces']);
			return $res;
		}

		function call_form_info($_ROUTE_PARAMS)
		{
			$qres=null;
			
			$opts=['query'=>$_ROUTE_PARAMS,'onhandle'=>function($modname,$ev_res,$_continue) use (&$qres)
			{
			//	print_dbg($modname."::");
			//	print_dbg($ev_res);
				if($ev_res!=null)
				{
					//$_continue = false;
				//	print_dbg($ev_res);
					$qres = $ev_res;
				}
			}];
			$_json_res=[];
			$args=['route'=>$_ROUTE_PARAMS];
			$query_res = $this->call_event('oncallform',$args,$opts);
		//	print_dbg($qres);
			return $qres;
		}

		function call_obj($_route,$obj_class=null)   
		{
	
			$obj_nfo = $this->obj_info_by_route($_route);

			switch($obj_nfo['obj_class'])
			{
				case 'forms': {
					$_obj_form = $this->load_form($obj_nfo);
					//return $_obj_form;
				} break;
			}
			return null;
		}
		
		function restbox_route_onquery(&$eargs)
		{	
			$obj_res = $this->call_routed_obj($eargs['route']);			
			return $obj_res;
		}	
			
		function AfterLoad()
		{
			//	
		}

	
	}

}
