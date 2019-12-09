<?php

namespace modules\restbox {
    use Core;
    use Core\Router as Router;

    class AppObject
    {
        VAR $CFG_INFO;

        function __construct($_req_params,$cfg_info=[])
        {
            $this->CFG_INFO = $cfg_info;    
        }

        static function getKey($_req_params) // key to object map
        {

        }

        static function GetRoutePatterns()
        {

        }
        // search the request and action
        static function FindPattern($req_str)
        {
            $patterns = self::GetRoutePatterns();
            foreach($patterns as $ptrn => $_action)
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