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

        }

        function item($_request)
        {

        }

        function ExeAction($_req_params)
        {
            include $this->CFG_INFO['CFG_DIR']."/tables/".$_req_params['vars']['table'].".php";
            
            $_info_obj = $info->getInfo();
            
            return $_info_obj;
        }
   }
}