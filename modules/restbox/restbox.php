<?php


namespace modules\restbox {
	require_once '/inc/appobj.php';	
	require_once '/inc/obj_description.php';
	require_once '/inc/rbmodule.php';
	use Core;

	class Module extends \Core\Module
	{
		VAR $_CONF;
		VAR $_EP;
		VAR $_CONFIGS_AREA;
		VAR $_NO_READ_CONFIG;
		VAR $_CURR_CONF_DIR;
		VAR $_CONF_PATH;
		VAR $_SETTINGS;
		VAR $_CONFIG;
		VAR $_CFG_DIR;
		VAR $_CFG_INFO;
		
		function __construct($_PARAMS)
		{
		//	
			//echo "THIS IS RESTBOX";
			def_options(['cfgpath'=>'./restbox'],$_PARAMS);
			parent::__construct($_PARAMS);
			
			GLOBAL $_MUL_DBG_WORK;
		//	print_r($_MUL_DBG_WORK);	
		}
		
		function load_config()
		{	
			include "./conf.php";
			
			$this->_SETTINGS = $settings;
			$this->_CONFIG = $settings['curr_cfg'];


			$this->_CFG_DIR = url_seg_add($this->_L_SETTINGS['rbdir'],$this->_CONFIG);

			include url_seg_add($this->_CFG_DIR,'index.php');
			$this->_CFG_INFO = $cfg;

			//mul_dbg($this->_CFG_INFO,true,true);

			$res = $this->call_event('after_load_config',$args,$opts);
		}

		function get_settings()
		{
			$stngs = $this->_CFG_INFO;
			$stngs['CFG_DIR'] = $this->_CFG_DIR;
			return $stngs;
		}
			
		function AfterLoad()
		{
			
			$this->load_config();
			//
			$opts=[];
			$_json_res=[];
			$args=[];
			$this->call_event('onload',$args,$opts);
			if(!isset($_GET['q']))
				$_GET['q']="";
			$args=['route'=>$_GET['q']];
			$res = $this->call_event('getresult',$args,$opts);

			//print_r($res);
			$this->result_out($res[ $this->_L_SETTINGS['rbrouter']]);
		}

		public function out_error($_err) // out errors
		{
			$_err_box=['error'=>$_err];
			$this->result_out($_err_box);
			exit();
		}

		function result_out($_res)
		{			
			header("Content-type: application/json; charset=utf-8");
			echo json_encode($_res);
		}
				
	}

}