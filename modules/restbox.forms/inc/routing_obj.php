<?php
namespace modules\restbox\forms {
    use modules\restbox as restbox;
    use Core\Router as Router;
    

   class RoutingObj extends restbox\AppObject {

        VAR $_CONN_ID;
        VAR $authroles;  
        VAR $request;

        function __construct($_req_params,$cfg_info=[],$pmodule=null)
        {
            parent::__construct($_req_params,$cfg_info,$pmodule);
            $this->request = $_req_params;
        }

        function exe_action($action,$_req_pieces)
        {
            

            return $this->$action($_req_pieces);
        }

        static function GetDefAction()
        {
            return "AInfo";  
        }

        function load_me()
        {
            
        }
        
        function AInfo()
        {
            
        }

    }

}