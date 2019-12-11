<?php
namespace modules\restbox\table {
    use modules\restbox as restbox;
    

   class ObjTable extends restbox\AppObject {
   
        function __construct($_req_params,$cfg_info=[],$pmodule=null)
        {
           parent::__construct($_req_params,$cfg_info,$pmodule);
        }

        static function getKey($_req_params) // key to object map
        {
         //   print_dbg($_req_params);

            return "tables/".$_req_params['vars']['table'];
        }

        static function GetRoutePatterns()
        {
            return [
                    'tables/:table:'=>'view',
                    'tables/:table:/:id:'=>'item',
                ];
        }

        function view($_request)
        {
            include $this->CFG_INFO['CFG_DIR']."/tables/".$_request['vars']['table'].".php";
            $this->call_mod_func('restbox.db', 'query',"SELECT * FROM {$_request['vars']['table']}");
            return ['view'=>1];
        }

        function item($_request)
        {

        }

   }
}