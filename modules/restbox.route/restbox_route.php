<?php
namespace modules\restbox\route {
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
		
		function __construct($_PARAMS)
		{
			//echo "THIS IS RESTBOX";
			parent::__construct($_PARAMS);
			$this->load_api();
			
		}
		
		function load_api()
		{

		}
			
		function AfterLoad()
		{
			//
	
		}

		function register_route($route)
		{

		}

		private function get_obj_by_route($route,$add_data=[]) // получить результат отдельного запроса
		{
			//$this->
			$qres=null;
			$opts=['query'=>$route,'onhandle'=>function($modname,$ev_res,$_continue) use (&$qres)
			{
			//	print_r($ev_res);
				if($ev_res!=null)
				{
					$_continue = false;
					$qres = $ev_res;
				}
			}];
			$_json_res=[];
			$args=[];
			$query_res = $this->call_event('onquery',$args,$opts);
	
			return $qres;
			/*
			$route_res = ['query'=>$route,
					'result'=> $qres,//['text'=>'Per aspera ad astra']
				];
			return $route_res; */
		}
        
        function restbox_onload(&$args)
        {
            
		}

		function query($arg_str)
		{

		}
		
		function restbox_getresult(&$args)
        {			
		//	print_r($args);

			return $this->get_obj_by_route($args['route']);
        }
	}

}