<?php
namespace modules\restbox\forms {
    use modules\restbox as restbox;
    use Core\Router as Router;
    

   class RoutingObj extends restbox\AppObject {

        VAR $_CONN_ID;
        VAR $authroles;  

        function __construct($_req_params,$cfg_info=[],$pmodule=null)
        {
            parent::__construct($_req_params,$cfg_info,$pmodule);
        }

        static function GetDefAction()
        {

        }
        
    }

}