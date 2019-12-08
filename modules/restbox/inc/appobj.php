<?php

namespace modules\restbox {
	use Core;

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

        function ExeAction($_req_params)
        {

        }
    }

}