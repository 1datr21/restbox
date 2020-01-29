<?php
namespace modules\restbox\forms {
	use Core;
    use Exception;
	use modules\restbox\RBModule as RBModule;
	
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
				'forms'=>'modules\restbox\forms\ObjForm'
			];
		}

		function obj_info_by_route($_route)
		{
			$res=[];
			try{
				$r_pieces = explode('/',$_route);
				$res['obj_class'] = $r_pieces[0];
				
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
					$action_name = $class_name::GetDefAction();
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

		function load_form($f_info)
		{
			$_cfg_info = $this->exe_mod_func('restbox', 'get_settings');
		//	print_dbg($_cfg_info ) ;
			$form_cfg = url_seg_add($_cfg_info['CFG_DIR'],'forms',$f_info['object']['name']).".php";
			if(!file_exists($form_cfg))
			{
				$this->exe_mod_func('restbox','out_error',['message'=>"Form {$f_info['object']['name']} not exists",'errno'=>54]);
				return;
			}
			$obj_class_name = $this->_obj_map[$f_info['obj_class']];
			
			include $form_cfg;
			$form_obj = new $obj_class_name($info);
			//$form_obj->exe_submit($data);
			return $form_obj;
		}

		function call_obj($_route,$obj_class=null)   
		{
		//	print_dbg($_route);			
			$obj_nfo = $this->obj_info_by_route($_route);
			//print_dbg($obj_nfo);

			switch($obj_nfo['obj_class'])
			{
				case 'forms': {
					$_obj_form = $this->load_form($obj_nfo);
					//return $_obj_form;
				} break;
			}


		/*	if($r_pieces[0]=='forms')
			{
				$form_name = $r_pieces[1];
				array_shift($r_pieces);
				array_shift($r_pieces);
			//	print_dbg($r_pieces);
				$_cfg_info = $this->exe_mod_func('restbox', 'get_settings');
				$form_script = url_seg_add($_cfg_info['CFG_DIR'],'forms',$form_name).".php";
				if(!file_exists($form_script))
				{
					$this->exe_mod_func('restbox','out_error',['message'=>"Form not with name $form_name exists",'errno'=>150]);
				}
				print_dbg($form_script ) ;
			}
		*/
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
			$obj_res = $this->call_obj($eargs['route']);
			
			return $obj_res;
		}	
			
		function AfterLoad()
		{
			//	
		}

	
	}

}
