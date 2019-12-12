<?php
namespace modules\restbox\db {
	use Core;
	use modules\restbox\RBModule as RBModule;
	require_once '/inc/rbdbdriver.php';

	class Module extends RBModule 
	{
		VAR $_CONF;
		VAR $_EP;
		VAR $_CONNECTIONS;
		VAR $_DEF_CONN_IDX;
		
		function __construct($_PARAMS)
		{
			
		}

		public function query($qargs,$conn_id=null,$params=[])
		{
		//	print_dbg($qargs);
		}

	

		function query_text_select($_params)
		{

		}
		
		function restbox_after_load_config($args)
		{
			$rb_info = $this->exe_mod_func('restbox','get_settings');
			if(isset($rb_info['connection']))
			{
				$module_drv = "restbox.db.".$rb_info['connection']['drv'];
				$conn = $this->exe_mod_func($module_drv,'connect',$rb_info['connection']);
				if(is_array($conn))
				{
					if(isset($conn['error']))
					{
						$this->exe_mod_func('restbox','out_error',$conn['error']);
					}
				}

				
				$this->_CONNECTIONS[]=$conn;
				if(count($this->_CONNECTIONS)==1)
				{
					$this->_DEF_CONN_IDX=0;
				}

				return count($this->_CONNECTIONS)-1;
			}
			//mul_dbg($rb_info,true,true);
		}
	}

}