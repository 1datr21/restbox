<?php

namespace modules\restbox {
    use Core;
    use Core\Router as Router;

    class AppObject
    {
        VAR $CFG_INFO;
        VAR $P_MODULE;

        function __construct($_req_params,$cfg_info=[],$pmodule=null)
        {
            $this->CFG_INFO = $cfg_info;
            $this->P_MODULE = $pmodule;    
        }

        static function getKey($_req_params) // key to object map
        {

        }

        static function GetRoutePatterns()
        {

        }
        // search the request and action
        static function FindPattern($req_str,$ptrn_list)
        {
            
            foreach($ptrn_list as $ptrn => $_action)
            {
                $router = new Router($ptrn);
                $_match = $router->match($req_str);
                if($_match!==false)
                {
                    return [ 'action'=>$_action, 'request' => $_match ];
                }
            }
            return null;
        }

        function ExeAction($_req_params)
        {

        }
    }

}