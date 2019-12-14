<?php
namespace modules\restbox\table {
    use modules\restbox as restbox;
    

   class ObjTable extends restbox\AppObject {
   
        VAR $_CONN_ID;

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

        function connect_db($dbparams)  // connect the database
        {
         
       //     print_dbg( 'exists: '.$this->call_mod_func('restbox.db','connection_exists',$this->_CONN_ID) );
            if(! $this->call_mod_func('restbox.db','connection_exists',$this->_CONN_ID) )
            {
                
                $this->call_mod_func('restbox.db','connect',$dbparams,$this->_CONN_ID);    
            }
        }

        function beforeAction($_req_params)
        {
            $rb_info = $this->P_MODULE->exe_mod_func('restbox','get_settings');

            def_options(['conn_id'=>0],$_req_params['vars']);
            
            $this->_CONN_ID = $_req_params['vars']['conn_id'];
            $_db_info = $rb_info['connections'][$this->_CONN_ID];

            $this->connect_db($_db_info);
        }

        function view($_request)
        {
            include $this->CFG_INFO['CFG_DIR']."/tables/".$_request['vars']['table'].".php";
            $res = $this->call_mod_func('restbox.db', 'query',"SELECT * FROM `@+{$_request['vars']['table']}`");
            $rows=[];
            while($row = $this->call_mod_func('restbox.db', 'fetch_object',$res))
            {
                $rows[]=$row;
            }
            return $rows;
        }

        function item($_request)
        {
            include $this->CFG_INFO['CFG_DIR']."/tables/".$_request['vars']['table'].".php";
        //    print_dbg($_request);
            $res = $this->call_mod_func('restbox.db', 'query',"SELECT * FROM `@+{$_request['vars']['table']}` WHERE id={$_request['vars']['id']}");
            $rows=[];
            while($row = $this->call_mod_func('restbox.db', 'fetch_object',$res))
            {
                $rows[]=$row;
            }
            return $rows;    
        }

   }
}