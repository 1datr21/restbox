<?php
namespace modules\restbox\forms {
	use Core;
    use Exception;
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

		function obj_info_by_route($_route)
		{
			$res=[];
			try{
				$r_pieces = explode('/',$_route);
				$res['obj_class'] = $r_pieces[1];
				$res['object']=['name'=>$r_pieces[2],'type'=>'config'];
				array_shift($r_pieces);
				array_shift($r_pieces);
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
			print_dbg($_cfg_info ) ;
			$form_cfg = url_seg_add($_cfg_info['CFG_DIR'],'forms',$f_info['object']['name']).".php";
			if(!file_exists($form_cfg))
			{
				$this->exe_mod_func('restbox','out_error',['message'=>"Form {$f_info['object']['name']} not exists",'errno'=>54]);
			}
			//print_dbg($form_cfg ) ;
		}

		function call_obj($_route,$obj_class)   
		{
		//	print_dbg($_route);			
			$obj_nfo = $this->obj_info_by_route($_route);

			switch($obj_nfo['obj_class'])
			{
				case 'forms': {
					$_obj_form = $this->load_form($obj_nfo);
				} break;
			}

		/*	if($r_pieces[0]=='forms')
			{
				$form_name = $r_pieces[1];
				array_shift($r_pieces);
				array_shift($r_pieces);
			//	print_dbg($r_pieces);
				$_cfg_info = $this->exe_mod_func('restbox', 'get_settings');
				$form_cfg = url_seg_add($_cfg_info['CFG_DIR'],'forms',$form_name).".php";
				if(!file_exists($form_cfg))
				{
					$this->exe_mod_func('restbox','out_error',['message'=>"Form $form_name not exists",'errno'=>54]);
				}
				print_dbg($form_cfg ) ;
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
			$obj_res = $this->call_obj($eargs['route'],'modules\restbox\forms\ObjForm');
			
			return $obj_res;
		}	
			
		function AfterLoad()
		{
			//	
		}

	
	}

}
