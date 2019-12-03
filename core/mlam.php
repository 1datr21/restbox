<?php
namespace Core {

	// module loader and manager
	
	class MLAM {
	
		var $_BASE_DIR='';
		var $_MODULES_OBJS=[];
		var $_SETTINGS=[];
		var $_MUST_SAVE=[];
		var $_LOADING_QUEUE=[];
		var $_MODULES_DIR='';
	
		function load_modules()
		{
			// load modules settings
			GLOBAL $_BASEDIR;
			
			$this->_MODULES_DIR = url_seg_add($_BASEDIR,'modules');
			
			$fp_settings  = new FilePair( url_seg_add($this->_MODULES_DIR,"settings.php"));
			//
			$this->_SETTINGS = $fp_settings->get_settings();		
					
			$modules = get_files_in_folder($this->_MODULES_DIR,['dirs'=>true,'basename'=>true]);
			
			foreach ($modules as $mod)
			{							
					
				$mod_make_res = $this->load_module($mod);
			}
			// ������� ����� ��������
			foreach ($this->_MODULES_OBJS as $_modname => $mod_obj)
			{
				$this->_MODULES_OBJS[$_modname]->AfterLoad();
				//$this->_AfterLoad($_modname);
			}
		}
		
		function _AfterLoad($_modname)
		{
			if(!in_array($_modname,$this->_AFTERLOAD_EXECUTED))
			{
				$reqs = $this->_MODULES_OBJS[$_modname]->required();
				foreach ($reqs as $reqmod)
				{
					$this->_AfterLoad($reqmod);
				}
				$this->_MODULES_OBJS[$_modname]->AfterLoad();
				
				$this->_AFTERLOAD_EXECUTED[]=$modname;
			}
		}
	
		function get_mod_class_name($mod)
		{
			return "modules\\".str_replace('.', '\\', $mod)."\Module";
		}
	
		private function main_mod_file_name($mod)
		{
			GLOBAL $_BASEDIR;
			return url_seg_add($_BASEDIR,"./modules/$mod/".strtr($mod,'.','_').".php");
		}

		function exe_function($_mod,$func,$params=[])
		{
			return $this->_MODULES_OBJS[$_mod]->$func($params);
		}
		// ������� ������ ������
		function load_module($mod)
		{
			try{
				
				if($this->module_loaded($mod)) // ������ ��� ��������
					return true;
						
				if(!$this->module_enabled($mod)) // ������ ����������
				{
					return false;
				}
				
				$_main_file = $this->main_mod_file_name($mod);
				
				
				if(file_exists($_main_file))
				{
				//	echo $_main_file;
					require_once $_main_file;
				}
				else 
				{
					throw new MicronException("File of module $mod ($_main_file) is not exists");
				}
						
				$mod_class = $this->get_mod_class_name($mod);
				
				$mod_settings = $mod_class::settings();
				if($mod_settings['sess_save'])
				{
					$this->_MUST_SAVE[]=$mod;
					
					$mod_obj = $this->unserialize($mod);
					if($mod_obj===null)
					{
						$mod_obj = new $mod_class(['path'=> "".dirname($_main_file)]);
						$mod_obj->onload_basic();
					}
				}
				else
				{
					$mod_obj = new $mod_class(['path'=> "".dirname($_main_file)]);
				}
				$mod_obj->_MOD_NAME = $mod;
				$mod_obj->set_ME($this);
				// ����������� ���������
				if(isset($this->_SETTINGS['mod_params'][$mod]))
				{
					$mod_obj->set_load_settings($this->_SETTINGS['mod_params'][$mod]);
				}
				else 
				{
					$mod_obj->set_load_settings();
				}
				
				$req_modules = $mod_obj->required();
				foreach($req_modules as $req)
				{						
					if(!$this->load_module($req))
					{
						$this->gen_error("Module $req is disabled or not exists");
						return false;					
					}
				}
						
				$this->_MODULES_OBJS[$mod] = $mod_obj;
			}
			catch(MicronException $exc)
			{
				echo "<span>Exception : ".$exc->getMessage()."</span>"; 
				return false;
			}
	
			return true;
		}
		// �������������� ������, ������� �����
		public function save_modules()
		{
			foreach ($this->_MUST_SAVE as $_mod)
			{
				$this->serialize_module($_mod);
			}
		}
		// ������������� ������ 
		function serialize_module($modname)
		{
			$_mod_ser = serialize($this->_MODULES_OBJS[$modname]);
			if(!isset($_SESSION['_SER_MODS']))
			{
				$_SESSION['_SER_MODS']=[];
			}
			$_SESSION['_SER_MODS'][$modname]=$_mod_ser;
		}
		
		function unserialize($modname)
		{
			if(isset($_SESSION['_SER_MODS'][$modname]))
		   		return unserialize($_SESSION['_SER_MODS'][$modname]);
			return null;
		}
		// ��������� ������ � ������� ������
		public function call_module($mod_from,$mod_to,$evname,&$params)
		{
			if(isset($this->_MODULES_OBJS[$mod_to]))
			{
				$mrthod = $this->method_name($mod_from,$evname);
				return $this->_MODULES_OBJS[$mod_to]->$method($params);
			}
			else 
			{
				$this->err_log("Module not exists");
				return null;
			}
		}
		
		public function _call_module($modname,$method,$params)
		{
			if(isset($this->_MODULES_OBJS[$modname]))
			{
				return $this->_MODULES_OBJS[$modname]->$method($params);
			}
			else
			{
				$this->err_log("Module not exists");
				return null;
			}
		}
		
		function gen_error($err)
		{
			$this->call_event('mlam_error');
			echo "<font color='red'><h1>$err</h1></font>";
			die();
		}
		
		function err_log($err)
		{
			
		}
	
		function module_loaded($_mod)
		{
			return (isset($this->_MODULES_OBJS[$_mod]));
		}
		
		function module_exists($modname)
		{
			return (file_exists("./modules/$modname/index.php"));		
		}
		// check if module enabled
		function module_enabled($modname)
		{
			$res = false;
			foreach ($this->_SETTINGS['enable_modules'] as $idx => $word)
			{
				$res = match_mask($word,$modname);
				if($res) return  $res;
			}
			return $res;
		}	
		// get list of enabled modules
		function get_enabled_modules()
		{
			$modlist=[];
			$modules = get_files_in_folder('modules',['dirs'=>true,'basename'=>true]);
			foreach ($modules as $mod)
			{		
				if($this->module_enabled($mod))
				{
					$modlist[]=$mod;
				}
			}
			return $modlist;
		}
		
		function get_modules_by_mask($mask)
		{
			$modules = $this->get_enabled_modules();
			$modlist=[];
			foreach ($modules as $mod)
			{
				if(match_mask($mask, $mod))
				{
					$modlist[]=$mod;
				}
			}
			return $modlist;
		}
		
		function call_modules($eventname,$args=[],$eopts=[])
		{
			def_options(array('src'=>'module'), $eopts);
	
			$called_list=array();
		//	print_r($this->_MODULES_OBJS);

			foreach ($this->_MODULES_OBJS as $idx => $mod)
			{
				if(($mod->get_mod_name()!=$module)&&(!in_array($mod,$called_list)))
				{
				//	$this->call_event($mod,$eventname,$module,$called_list,$args, $eopts);
					$ev_res = $this->call_event_for_module($mod,$eventname,$args);
				}
			}
			return $args;
		}

		function call_event_for_module($mod_obj,$_event,$_params=[],$priority=null)
		{
			$ev_func_name = $this->event_function_name($_event);
			if(method_exists($mod_obj, $ev_func_name))
				{
					$ev_res = $mod_obj->$ev_func_name($_params);
					return $ev_res;
					//$ev_results[$modname]=$ev_res;
				}
			return null;
		}

		// call event
		function call_event($_event,$_params=[],$opts=[])
		{
			$mod_keys = array_keys($this->_MODULES_OBJS);
			$mod_keys_new = [];
			def_options(['priority'=>null],$opts);

			if($opts['priority']!=null) // ���� ����� ��������� ��������� �������
			{			
				foreach ($opts['priority'] as $pr_element)
				{
					if(is_mask($pr_element))
					{
						$bymask = $this->get_modules_by_mask($mask);
						$mod_keys_new = array_merge($mod_keys_new,$bymask);
					}
					else 
					{
						$mod_keys_new[]=$pr_element;
					}				
				}
				
				foreach ($mod_keys as $idx => $mod)
				{
					if(!in_array($mod, $mod_keys_new))
					{
						$mod_keys_new[]=$mod;
					}
				}
				
				$mod_keys = $mod_keys_new;
			}
			// ������������
			$ev_results = [];
			$_continue = true;
			do{
				$next = $this->make_event_loop($_event,$mod_keys,$_params,$opts,$ev_results);
			} while(count($next)>0);

		//	echo "events : ";
		//	print_r($ev_results);


			return $ev_results;
		}

		private function make_event_loop($_event,&$mod_keys,$_params,$opts,&$ev_results)
		{
			$_mods_next =[];
			$_continue = true;
			foreach ($mod_keys as $idx => $modname)
			{
				$mod_obj = $this->_MODULES_OBJS[$modname];
				$ev_func_name = $this->event_function_name($_event);
				$waiter_func_name = $ev_func_name."_waiter";
				// call waiter 
				$exe_ev = true;
				if(method_exists($mod_obj, $waiter_func_name))
				{
					$exe_ev = $mod_obj->$waiter_func_name($_params);
				}
				// exe method
				if($exe_ev && method_exists($mod_obj, $ev_func_name))
				{
					$ev_res = $mod_obj->$ev_func_name($_params);
					if(isset($opts['onhandle']))
					{
						$opts['onhandle']($modname,$ev_res,$_continue);	
					}
					$ev_results[$modname]=$ev_res;
					if(!$_continue)
						break;
				}
				if(!$exe_ev) // add to next loops
				{
					$_mods_next[]=$modname;
				}
			}

			return $_mods_next;
		}
	
		function call_event_sess($_event,$_params=[],$priority=null)
		{
			
			if(!isset($_SESSION['events']))
			{
				$_SESSION['events']=[];
			}
			$_SESSION['events'][]=['event'=>$_event,'params'=>$_params,'priority'=>$priority];
		}
		
		function exe_sess_events()
		{
		//	unset($_SESSION['events']);
			if(isset($_SESSION['events']))
			{
				foreach ($_SESSION['events'] as $_ev)
				{
					$this->call_event($_ev['event'],$_ev['params'],$_ev['priority']);
				}
				unset($_SESSION['events']);
			}
		}
		
		function event_function_name($ev_name)
		{
			return strtr($ev_name,".","_");
		}
	
	}

}