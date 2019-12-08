<?php
// ?????? ?????????????
namespace Core {

	class Router  
	{
		VAR $_MAP;

		function __construct($r_ptrn)
		{
			$this->_MAP = $this->route_ptr_map($r_ptrn);
		}
		
		function match($req_str)
		{
			$_result=array();
			
			print_dbg($this->_MAP);
			
			$pos_base = strpos($req_str, $this->_MAP['base']);
			//print_dbg($pos_base);
			if ($pos_base === false) 
				return FALSE;
		
			$str_end = substr($req_str,strlen($this->_MAP['base'])+1);
			$segments = explode('/',$str_end);
			
			foreach($segments as $seg)
			{
				$_pieces = explode(':',$seg);
				if(count($_pieces)>0)
				{
					if(isset($this->_MAP['vars'][$_pieces[0]]))
						$_result[$_pieces[0]]=$_pieces[1];
				}
				else
				{
				/*	foreach($this->_MAP['vars'] as $var => $_required) 
					{
						print_dbg("<<".$var);

						if(!isset($_result[$var]))
						{
							$_result[$var] = $seg;
						}
					} */
				}
			//	print_dbg($_pieces);
			}
			
			return $_result;
		}
		
		function route_ptr_map($r_ptrn)
		{
			$res_map = [];
			$exploded = explode('/',$r_ptrn);
			foreach($exploded as $expl)
			{
				$map=[];
				preg_match_all("#\[(.+)\]#Uis",$expl,$map);
				$_required=true;
				$_var = false;
				$seg_name = '';

					//print_dbg($map);

				if(count($map[0])==0)
				{
						
						$seg_name = $expl; 
						
					//	$res_map[] = ['content'=>$expl,'type'=>'const'];	
				}
				else
				{
					$_required = false;
					$expl = $map[1][0];
				}
											
				preg_match_all("#:(.+):#Uis",$expl,$map);
					//	print_dbg($map);
				if(count($map[0])==0)
				{
						
				}
				else
				{
					$seg_name = $map[1][0]; 
					$_var = true;
				}	
					
				$res_map[] = [
					'seg_name'=>$seg_name,
					'required'=>$_required,
					'var'=>$_var
				];
					
			}
			
			$str_base="";
			$vars=[];
			foreach($res_map as $_map_elem)
			{
					if(!$_map_elem['var'])
					{
						if($str_base =="")
							$str_base = $_map_elem['seg_name'];
						else
							$str_base = $str_base."/".$_map_elem['seg_name'];
					}
					else
					{
						unset($_map_elem['var']);
						$vars[$_map_elem['seg_name']]=$_map_elem['required'];
					}
			}
				//print_dbg($str_base);
			return ['base'=>$str_base,'vars'=>$vars];
		}

			
	}

}