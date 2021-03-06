<?php


namespace modules\restbox\table {
	use Core;
	use Core\Router as Router;
    use Exception;
    use modules\restbox\AppObject;
	use modules\restbox\RBModule as RBModule;

	require_once 'inc/ftypes/ft_basic.php';
	require_once 'inc/obj_table.php';

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

		function restbox_route_onquery(&$eargs)// bind all routes to object routes
		{				
			$obj_res = $this->call_obj($eargs['route'],'modules\restbox\table\ObjTable');
			
			return $obj_res;
		}
		
		function save($table,$rec_data)
		{
			$obj = $this->make_obj_or_return('modules\restbox\table\ObjTable', $table);
			//print_dbg($obj);
			$obj->save(['vars'=>['table'=>$table]],$rec_data);
			//$obj_res = $this->call_obj('tables/save/'.$table,'modules\restbox\table\ObjTable',2);
			
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
					'password'=>['file'=>'/inc/ftypes/ft_password.php','ns'=>$ns_def,'class'=>'ft_password'],
					'int'=>['file'=>'/inc/ftypes/ft_int.php','ns'=>$ns_def,'class'=>'ft_int'],
					'float'=>['file'=>'/inc/ftypes/ft_float.php','ns'=>$ns_def,'class'=>'ft_float'],
					'datetime'=>['file'=>'/inc/ftypes/ft_datetime.php','ns'=>$ns_def,'class'=>'ft_datetime'],
					'date'=>['file'=>'/inc/ftypes/ft_datetime.php','ns'=>$ns_def,'class'=>'ft_datetime'],
					'enum'=>['file'=>'/inc/ftypes/ft_enum.php','ns'=>$ns_def,'class'=>'ft_enum'],
					'set'=>['file'=>'/inc/ftypes/ft_set.php','ns'=>$ns_def,'class'=>'ft_set'],
					'bool'=>['file'=>'/inc/ftypes/ft_bool.php','ns'=>$ns_def,'class'=>'ft_bool'],
					'file'=>['file'=>'/inc/ftypes/ft_file.php','ns'=>$ns_def,'class'=>'ft_file'],
					'ref'=>['file'=>'/inc/ftypes/ft_ref.php','ns'=>$ns_def,'class'=>'ft_ref'],
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

		function get_conf_settings()
		{
			return $this->_CONF;
		}

		function load_table($_table)
		{
			//print_dbg($this);
			try{
				$this->CFG_INFO = $this->exe_mod_func('restbox','get_settings');
				$table_file = $this->CFG_INFO['CFG_DIR']."/tables/{$_table}.php";
				if(!file_exists($table_file))
					return null;
				include $table_file;
				//    
				$info_obj = $this->build_info($info,$_table);

				//print_dbg($info_obj);
				return $info_obj;
			}
			catch(Exception $exc)
			{
				return null;
			}
		}

		function restbox_forms_oncallform($eparams)
		{
		//	print_dbg($eparams);
			if($this->load_table($eparams['route']['object']['name'])!=null)
			{
				$res = $this->call_obj('tables/'.$eparams['route']['object']['name'].'','modules\restbox\table\ObjTable',2);
			//	print_dbg($res);
				return $res;
			}
			
			return null;
		}

		function build_info($info,$tname)
        {
			//print_dbg($info);
			
            $res = new TableMap($tname,$info,$this);
            foreach($info->_info['fields'] as $fld => $fldinfo)
            {
			//	print_dbg($fldinfo);
				
               $fld_obj = $this->load_ftype($fld,$fldinfo);
               $res->add_field($fld,$fld_obj);
            }
            return $res;
        }

		function load_ftype($ftname,$ftparams,$required=true)
		{			
			$this->gather_ftypes();	

			if(isset($this->_F_TYPES[$ftparams->_ftype]))
			{
				require_once $this->_F_TYPES[$ftparams->_ftype]['file'];

				$ftclass = strtr(url_seg_add($this->_F_TYPES[$ftparams->_ftype]['ns'],$this->_F_TYPES[$ftparams->_ftype]['class']),['/'=>'\\']);

				return new $ftclass($ftparams->_info,$ftname,$this);
			}
			return null;
		}
	}  

	class tfield {
		VAR $_info;
		VAR $_ftype;

		function __construct($_ftype,$_PARAMS=[],$require='null')
		{						
			if(is_array($_PARAMS))
			{
			}
			else
			{
				$require = $_PARAMS;
				$_PARAMS=[];
			}
			$req_map = ['no'=>false,'required'=>true,false=>false, true=>true];
			if(isset($req_map[$require]))
			{
				$_PARAMS['require'] = $req_map[$require];
			}
			$this->_info = $_PARAMS;			
			$this->_ftype = $_ftype;
		}
		
		
		
	}

}