<?php
// ?????? ?????????????
define('__request_descr__', 'r');


class Router  
{
	VAR $_DIR_CONFIG;
	VAR $_DIR_EP;
	VAR $_THEME;
	VAR $CFG_INFO;
	VAR $_ENV_INFO=array();
	
	function __construct($_PARAMS)
	{
		
	}
	
	static function get_alternative_request($req_obj)
	{
		
	}
	
	static function parse_request($req_str)
	{

		$_result=array();
			
		$def_controller='site';
		$def_action='index';
		$req_slices = explode('/', $req_str);
		$argidx=0;
		$str_varval="";
		foreach ($req_slices as $idx => $slice)
		{
			if(empty($slice))
					continue;
			list($varname,$varval)=explode(':',$slice);
			if(empty($varval))
					{
						if($idx==0)
						{
							$_result['controller']=$slice;
						}
						elseif($idx==1)
						{
							$_result['action']=$slice;
						}
						else
						{
							$_result['args'][$argidx]=$slice;
							$argidx++;
						}
					}
					else
			{
			if($str_varval!="")
				$str_varval=$str_varval."&";
			$str_varval=$str_varval."{$varname}={$varval}";
							//$_REQUEST[$varname] = $varval;
			}
					//echo "$varname : $varval |";
		}
			
		if(!empty($str_varval))
		{
			$parsed = array();
			parse_str($str_varval,$parsed);
			$_result= array_merge($_result,$parsed);
		}
		
		return $_result;
	}
	
	static function make_url($_query_hash,$to_change=array(),$to_delete=array())
	{
		$str = $_query_hash['controller'];
		if($_query_hash['action'])
		{
			if($_query_hash['action']!='index')
				$str = url_seg_add($str,$_REQUEST['action']);
		}
		if(!empty($_query_hash['args'] ))
		{
			foreach ($_query_hash['args'] as $arg)
			{
				$str=url_seg_add($str,$arg);
			}
		}
		foreach ($_REQUEST as $key => $var)
		{
			if(!in_array($key, array('controller','action','args')))
			{
				if(!empty($to_change[$key]))
				{
					$val = $to_change[$key]; 
					$str = url_seg_add($str,"$key:$val");
				}
				else 
					$str = url_seg_add($str,"$key:$var");
			}
		}
		return $str;
	}
		
}