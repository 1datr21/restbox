<?php
namespace modules\restbox\table {
    use modules\restbox as restbox;
    

   class ObjTable extends restbox\AppObject {
   
        function __construct($_req_params,$cfg_info=[])
        {
           parent::__construct($_req_params,$cfg_info);
        }

        static function getKey($_req_params) // key to object map
        {
            return "tables/".$_req_params['vars']['table'];
        }

        static function get_ns_name($_req_params)
        {
            return 'tables\\'.$_req_params['vars']['table'];
        }

        function ExeAction($_req_params)
        {
            include $this->CFG_INFO['CFG_DIR']."/tables/".$_req_params['vars']['table'].".php";
            
            $_info_obj = $info->getInfo();
            
            return $_info_obj;
        }
   }
}