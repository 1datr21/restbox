<?php
namespace modules\restbox\forms {
    use modules\restbox as restbox;
    use Core\Router as Router;
    

   class ObjForm extends restbox\AppObject {

        VAR $_CONN_ID;
        VAR $authroles;

        function __construct($_req_params,$cfg_info=[],$pmodule=null)
        {
            parent::__construct($_req_params,$cfg_info,$pmodule);
        }

        static function GetRoutePatterns()
        {
            return [
                    'forms/:table:'=>'view',
                ];
        }

        static function FindPattern($req_str,$ptrn_list)
        {
        //    print_dbg($req_str);

            foreach($ptrn_list as $ptrn => $_action)
            {
                $router = new Router($ptrn);
                $_match = $router->match($req_str);
                if($_match!==false)
                {
                    return [ 'action'=>$_action, 'request' => $_match ];
                }
            }
            return false;   
        }        

        function connect_db($dbparams)  // connect the database
        {
         
       //     print_dbg( 'exists: '.$this->call_mod_func('restbox.db','connection_exists',$this->_CONN_ID) );
            if(! $this->call_mod_func('restbox.db','connection_exists',$this->_CONN_ID) )
            {
                
                $this->call_mod_func('restbox.db','connect',$dbparams,$this->_CONN_ID);    
            }
        }

        function beforeAction($_req_params)
        {
            $rb_info = $this->P_MODULE->exe_mod_func('restbox','get_settings');

            def_options(['conn_id'=>0],$_req_params['vars']);
            
            $this->_CONN_ID = $_req_params['vars']['conn_id'];
            $_db_info = $rb_info['connections'][$this->_CONN_ID];
         //   print_dbg('connectingg');

            $this->connect_db($_db_info);
        }


        function search_fld_by_synonims($fldbuf,$syn_array)
        {
            if(!is_array($syn_array))
            {
                $syn_array = [$syn_array];
            }
            foreach($fldbuf as $_fld_name => $fld)
            {
                foreach($syn_array as $syn)
                {
                    if(stristr($_fld_name,$syn)!=FALSE)
                    {
                        return $_fld_name;
                    }
                }
            }
            return null;
        }

        function logout()
        {
         //   print_dbg('logout');
            $this->P_MODULE->unset_var('user_table_info');
            $this->P_MODULE->unset_var('user_id');

        //    $this->P_MODULE->clear_session();
        }
   }

}

