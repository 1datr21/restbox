<?php
namespace modules\restbox\table {
    use modules\restbox as restbox;

   class ObjTable extends restbox\AppObject {
   
        function __construct($params=[])
        {
           parent::__construct($params);
        }

        static function getKey($_req_params) // key to object map
        {
            return "table/".$_req_params['vars']['table'];
        }

        function ExeAction($_req_params)
        {

        }
   }
}