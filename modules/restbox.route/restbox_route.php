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
		}
        
        function restbox_onload(&$args)
        {
            
		}

		/* Format  
		?q=tables/users::q1;tables/task
		query1[::key1];query2[::key2];...queryN[::keyN]
s		*/
		function query($arg_str) // call this function from other units and configs
		{
			$query_segments = explode(';',$arg_str);

			
			$response = [];
			foreach($query_segments as $q_str) 
			{				
				$matches=[];
				if(preg_match_all( "#^(.+)\:\:([[:alnum:]]+)$#Uis",$q_str,$matches))
				{
					$q_str = $matches[1][0];
					$_q_id = $matches[2][0];
				}

				$_res_obj = $this->get_obj_by_route($q_str);
				
				$_new_query_obj=[
					'query'=>$q_str,
					'response'=>$_res_obj,
				];
				if(isset($_q_id))
				{
					$_new_query_obj['key']=$_q_id;
					unset($_q_id);
				}

				$response[]=$_new_query_obj;
			}
			return $response;
		}
		
		function restbox_getresult(&$args)
        {			
		//	print_r($args);

			return $this->query($args['route']);
        }
	}

}