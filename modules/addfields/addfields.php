<?php


namespace modules\addfields {
		
	use Core;
    use Core\Router as Router;
    use modules\restbox\RBModule as RBModule;

    require_once '/inc/enum.php';

	class Module extends RBModule 
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
			
		}
		
		function AfterLoad()
		{
		//	
		//	print_dbg(">> Hello ",true,true);
        }
        
        function restbox_table_get_f_types($_args)
        {
            //$ns_def = 'modules\\restbox\\table\\';
			return [
					'enum'=>['file'=>'/inc/enum.php','ns'=>'modules\\addfields\\','class'=>'ft_enum'],
				];
		}
		
		function restbox_db_onCreateTable($args)
		{
			//print_dbg(">> [".get_class($args['finfo'])."]");
			if(get_class($args['finfo'])=="modules\\addfields\\ft_enum")
			{
			//	$_fld_str = " `status` ENUM('val1','','val2','val3') NOT NULL ";
			// ALTER TABLE `tms_users` CHANGE `status` `status` ENUM('val1','','val2','val3') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'val1';
				//print_dbg( xx_implode($args['finfo']->PARAMS['values'],',',"'{%val}'") );
				return [
					'fld_seg'=>"`{$args['finfo']->fldname}` ENUM(".implode(',',transform_array( $args['finfo']->PARAMS['values'] ,"'{%val}'")).") NOT NULL"
				];
				//print_dbg( $args['finfo']);
			}
			
		}
		
		
				
	}

}