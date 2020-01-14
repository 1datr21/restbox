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
        //    print_dbg($this->P_MODULE);

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
            eval($eval_code);

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
           // print_dbg('action :'.$_action);
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