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
            return "table/".$_req_params['vars']['table'];
        }

        function ExeAction($_req_params)
        {
          //  print_dbg($this->CFG_INFO['CFG_DIR']);
        //    include $this->CFG_INFO['CFG_DIR']."/tables/".$_req_params['vars']['table'].".php";
            return ['xxx'=>'123456'];
        }
   }
}