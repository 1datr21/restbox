<?php
namespace modules\restbox\session {
    use modules\restbox as restbox;
    

   class ObjAuthTable extends restbox\AppObject {

        VAR $_CONN_ID;

        function __construct($_req_params,$cfg_info=[],$pmodule=null)
        {
            parent::__construct($_req_params,$cfg_info,$pmodule);
        }

        static function GetRoutePatterns()
        {
            return [
                    'auth/[:table:]'=>'auth',
                    'auth/logout'=>'logout',
                ];
        }

        function auth($_request)
        {
        //    $this->P_MODULE->auth();
        //    print_dbg($_request);
            $info_cfg = $this->call_mod_func('restbox.table','get_conf_settings',1);
            print_dbg($info_cfg);
            $table_info = $this->call_mod_func('restbox.table', 'load_table', $_request['vars']['table']);
           // print_dbg($table_info);
            return [true];  
        }

        function logout()
        {
            $this->P_MODULE->clear_session();
        }
   }

}
