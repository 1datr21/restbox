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
		
		function __construct($_PARAMS)
		{
		//	print_dbg(">> +++ ",true,true);
		}

		function AfterLoad()
		{
			$this->load_sess_saver();
			$this->_SSAVER->delete_garbage();
		}

		function load_sess_saver()
		{
			if(empty($this->sess_id))
				$this->sess_id = $this->get_rb_token();
			if(empty($this->_SSAVER))
				$this->_SSAVER= new std_SessSaver();
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
			//print_dbg($eargs['route']);

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
			$this->sess_id = GenRandStr(25);
		}

		function start_session()
		{
			$this->load_sess_saver();

			if(!isset($this->sess_id))
				$this->gen_token();
			//print_dbg("sess = ".$this->sess_id);

			$this->save_session();
			return $this->sess_id ;
		}

		function get_sess_vars()
		{
			$this->load_sess_saver();
			$this->_SESS_INFO = $this->_SSAVER->get($this->sess_id);
			return $this->_SESS_INFO;
		}

		function get_var($varname)
		{
			$this->get_sess_vars();
			if(isset($this->_SESS_INFO[$varname]))
				return null;
			return $this->_SESS_INFO[$varname];
		}

		function set_sess_var($varname,$varval)
		{
			$this->load_sess_saver();
			$this->_SESS_INFO[$varname]=$varval;
			$this->_SSAVER->save($this->sess_id,$this->_SESS_INFO);
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

		function sess_vars()
		{
			$this->load_sess_saver();
			return $this->_SSAVER->get($this->sess_id);
		}

		function var_exists($varname)
		{
			$this->load_sess_saver();
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

		function __construct($exp_time=13560)
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

		}

		function delete_garbage() // delete garbage sessions
		{
			$sess_file_list = glob("./sess/*.sess"); 	
			//print_dbg('garbage collecting');
			foreach($sess_file_list as $sessfile)	
			{
				$mtime = filemtime($sessfile);
				$delta = time()-$mtime;
			//	print_dbg("$sessfile >> $delta");
				if($delta>=$this->exp_time)
				{
				//	print_dbg($sessfile);
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
