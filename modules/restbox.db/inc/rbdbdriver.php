<?php

namespace modules\restbox\db {
	use Core;
	use modules\restbox\RBModule as RBModule;

	class RBDBDriver extends RBModule 
    {
        function query_select($_params)
		{
			def_options([
				'page_size'=>20,
				'use_page'=>true,
                'chunk_by'=>0,
                'page'=>1,
			],$_params);
			if($_params['use_page'])
			{
                $q_total = "SELECT COUNT(*) as t_count FROM @+".$_params['table']."";
                
                
                $l_0 = $_params['page_size']*($_params['page']-1);
                $q_page = "SELECT COUNT(*) as t_count FROM @+".$_params['table']." LIMIT {$l_0 },{$_params['page_size']}";
			}
			else
			{

			}
			$query = "";
        }

        public function connect($_dbcfg)
        {

        }

        function exe_query($_prepared_query)
        {

        }
        
        function prepare_query($q_text,$_params)
        {

        }
    }

    class RBDBConnection {

        function __construct($_params)
        {
            
        }
    }
}