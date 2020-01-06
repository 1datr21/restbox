<?php
namespace modules\restbox\session {
    use modules\restbox as restbox;
    

   class ObjTable extends restbox\AppObject {

        VAR $_CONN_ID;

        function __construct($_req_params,$cfg_info=[],$pmodule=null)
        {
            parent::__construct($_req_params,$cfg_info,$pmodule);
        }

        static function GetRoutePatterns()
        {
            return [
                    'auth/:table:'=>'auth',
                /*    'tables/one/:table:/:id:'=>'item',
                    'tables/save/:table:'=>'save',
                    'tables/delete/:table:'=>'delete',*/
                ];
        }
   }

}
