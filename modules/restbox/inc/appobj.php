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

        function call_mod_func($mod, $func)
        {
            $args_str='';
            $args = func_get_args();
            $_arg_arr=[];
			foreach($args as $idx => $val)
			{
                if($idx>=2)
                {
                    $_arg_arr[]='$args['.$idx.']';
                }
            }
            $result = null;
            $eval_code = '$result = $this->P_MODULE->exe_mod_func($mod,$func,'.implode(',',$_arg_arr).');';
        //    print_dbg($eval_code);
            eval($eval_code);
         //   $this->P_MODULE->exe_mod_func($mod,$func,$args_str);
            //$this->MLAM->exe_function($mod,$func,$args);
            return $result;
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
            return false;
        }

        function ExeAction($_action,$_req_params)
        {
            if(method_exists($this,$_action))
            {
                $this->beforeAction($_req_params);
                return $this->$_action($_req_params);
            }
            return null;
        }

        function beforeAction($_req_params)
        {

        }
    }

}