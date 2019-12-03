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

		function get_obj_by_route($route,$add_data=[]) // получить результат отдельного запроса
		{
			//$this->
			$opts=['query'=>$route,'onhandle'=>function($modname,$ev_res,$_continue)
			{
				if($ev_res!=null)
				{
					$_continue = false;
				}
			}];
			$_json_res=[];
			$args=[];
			$query_res = $this->call_event('onquery',$args,$opts);

			$route_res = ['query'=>$route,
					'result'=>['text'=>'Per aspera ad astra']];
			return $route_res;
		}
        
        function restbox_onload(&$args)
        {
            
		}
		
		function restbox_getresult(&$args)
        {
            $args['json_result'] = $this->get_obj_by_route('hello');
        }
	}

}