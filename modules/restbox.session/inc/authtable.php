<?php
namespace modules\restbox\session {
    use modules\restbox as restbox;
    

   class ObjTable extends restbox\AppObject {

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

        function auth($req)
        {

        }

        function logout()
        {
            $this->P_MODULE->clear_session();
        }
   }

}
