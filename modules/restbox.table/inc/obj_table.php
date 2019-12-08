<?php
namespace modules\restbox\table {
    use modules\restbox as restbox;

   class ObjTable {
       function __construct()
       {
           
       }

       static function getKey($_req_params) // key to object map
       {
            return "table/".$_req_params['vars']['table'];
       }
   }
}