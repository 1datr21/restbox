<?php

namespace modules\restbox\session {
	use Core;
	use Core\Router as Router;
	use modules\restbox\AppObject;
	use modules\restbox\RBModule as RBModule;

	require_once '/inc/obj_authtable.php';

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
		VAR $sess_id;
		VAR $_SESS_INFO;
		VAR $_SSAVER;
		VAR $_renamed = false;
		VAR $_sess_settings;
		
		function __construct($_PARAMS)
		{
		
		//	print_dbg(">> +++ ",true,true);
		}

		function AfterLoad()
		{
			$all_settings = $this->exe_mod_func('restbox','get_settings');
			if(isset($all_settings['session']))
			{
				$this->_sess_settings = $all_settings['session'];
			}
			else
			{
				$this->_sess_settings = [];
			}

			def_options(['max-exp'=>1000,'time-to-rename'=>25,'rename_token'=>true], $this->_sess_settings);
			$this->WatchAll();
		}

		function WatchAll()
		{
			$this->load_sess_saver();
			$this->_SSAVER->delete_garbage();
			if(empty($this->sess_id))
				$this->sess_id = $this->get_rb_token();

		//	print_dbg("sid detected ".$this->sess_id);
			
			if(!empty($this->sess_id))
			{
				//print_dbg('sess lost');
				if(!$this->_SSAVER->exists($this->sess_id))
				{
					//$this->exe_mod_func('restbox','out_error',['message'=>'Session expired','errno'=>52]);
					$this->exe_mod_func('restbox','add_ext_data','SessExpired','true');
				}
			}
			
			$this->watch_to_rename();
		}

		function load_sess_saver()
		{
			if(empty($this->sess_id))
				$this->sess_id = $this->get_rb_token();
			if(empty($this->_SSAVER))
				$this->_SSAVER= new std_SessSaver($this->_sess_settings['max-exp']);
			
		}

		public function get_rb_token()
		{
			//$this->load_sess_saver();
			if(!empty(rtrim(ltrim($_SERVER['HTTP_RBTOKEN']))))
			{
				if($_SERVER['HTTP_RBTOKEN']=="null")
					return null;
				return $_SERVER['HTTP_RBTOKEN'];
			}
			return null;
		}

		function restbox_route_onquery(&$eargs)
		{				
			$obj_res = $this->call_obj($eargs['route'],'modules\restbox\session\ObjAuthTable');
			return $obj_res;
		}

		function clear_session()
		{
			$this->load_sess_saver();
			$this->_SSAVER->destroy($this->sess_id);
		}

		function gen_token()
		{			
			do {
				$_token = GenRandStr(25);
			} while($this->_SSAVER->exists($_token));
			return $_token;
		}

		function start_session()
		{
			$this->load_sess_saver();

			if(!isset($this->sess_id))
			{
				$this->sess_id = $this->gen_token();
			//	print_dbg("session started ".$this->sess_id);
				$this->_SESS_INFO['init_time']=time();
			}

			$this->save_session();
			return $this->sess_id ;
		}

		function get_sess_vars()
		{
			
			$this->_SESS_INFO = $this->_SSAVER->get($this->sess_id);
		//	print_dbg('sid is '.$this->sess_id);
		//	print_dbg('sess info');
		//	print_dbg($this->_SESS_INFO);
	
			return $this->_SESS_INFO;
		}

		function sess_vars(){
			return $this->get_sess_vars();
		}

		function get_var($varname,$defval=null)
		{
			$this->get_sess_vars();
		//	print_dbg($this->_SESS_INFO);			
			if(!isset($this->_SESS_INFO[$varname]))
			{
				if($defval!=null)
				{
					$this->start_session();
					$this->set_sess_var($varname,$defval);
				}
				return null;
			}
			return $this->_SESS_INFO[$varname];
		}

		function watch_to_rename($exp_to_rename=50)
		{
			if(!$this->_sess_settings['rename_token'])
			{
				return;
			}

			if(empty($this->sess_id))
				return;
			if($this->_renamed) return;

			if(!$this->_SSAVER->exists($this->sess_id))	// if session not exists - return
				return;

			$time = $this->get_var('init_time');		

			if(time()-$time >= $this->_sess_settings['time-to-rename'])
			{
				$old_sid = $this->sess_id;
				$new_sid = $this->gen_token();

				$this->set_sess_var('init_time',time());

				$this->_SSAVER->rename($old_sid,$new_sid);

			//	print_dbg("rename session $old_sid to $new_sid ");

				$this->sess_id = $new_sid;

				$this->exe_mod_func('restbox','add_ext_data','SESS_ID',$new_sid);
				$this->_renamed = true;
			}
		}

		function set_sess_var($varname,$varval)
		{
			$this->load_sess_saver();
			$this->_SESS_INFO[$varname]=$varval;
		//	print_dbg($this->_SESS_INFO);
			$this->_SSAVER->save($this->sess_id,$this->_SESS_INFO);
		}

		function set_var($varname,$varval)
		{
			$this->set_sess_var($varname,$varval);
		}

		function unset_var($varname)
		{
			$this->load_sess_saver();
			unset($this->_SESS_INFO[$varname]);
			$this->_SSAVER->save($this->sess_id,$this->_SESS_INFO);
		}

		function sess_exists()
		{
			$this->load_sess_saver();
			return !is_null($this->sess_id);
		}

		

		function var_exists($varname)
		{
			$this->load_sess_saver();
			$this->_SESS_INFO = $this->_SSAVER->get($this->sess_id);
			return isset($this->_SESS_INFO[$varname]);
		}

		function change_token()
		{

		}

		function sess_expired()
		{
			//filemtime()
		}


		function save_session()
		{			
			$this->_SSAVER->save($this->sess_id,$this->_SESS_INFO);
		}
		
	}

	class std_SessSaver{

		VAR $exp_time;

		function __construct($exp_time=723)
		{
			$this->exp_time = $exp_time;
		}

		function sess_file_path($sid)
		{			
			return "./sess/{$sid}.sess";
		}

		function save($sid,$vars)
		{
			$this->delete_garbage();
			x_file_put_contents($this->sess_file_path($sid),serialize($vars));
		}

		function get($sid)
		{
			$this->delete_garbage();
			if($this->exists($sid))
			{
				$ser_vars = file_get_contents($sess_path = $this->sess_file_path($sid));
				return unserialize($ser_vars);
			}
			return null;
		}

		function rename($sid,$sid_new_name)
		{
		
			rename($this->sess_file_path($sid), $this->sess_file_path($sid_new_name));
		}

		function get_modify_time($sid)
		{
			return filemtime($this->sess_file_path($sid));
		}

		function get_create_time($sid){
			return filectime($this->sess_file_path($sid));
		}

		function delete_garbage() // delete garbage sessions
		{
			$sess_file_list = glob("./sess/*.sess"); 	
			//print_dbg('garbage collecting');
			foreach($sess_file_list as $sessfile)	
			{
				$mtime = filemtime($sessfile);
				$delta = time()-$mtime;
			//	print_dbg(time()." >> $mtime = $delta");
				if($delta>=$this->exp_time)
				{
				//	print_dbg("Deleting $sessfile");
					unlink($sessfile);
				}
			}	
			//print_dbg($sess_list);
		}

		function destroy($sid)
		{			
			unlink($this->sess_file_path($sid));
			$this->delete_garbage();
		}

		function exists($sid)
		{
			return file_exists($this->sess_file_path($sid));
		}
	}

}
