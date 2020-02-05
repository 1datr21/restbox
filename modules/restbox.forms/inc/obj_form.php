<?php
namespace modules\restbox\forms {
    use modules\restbox as restbox;
    use Core\Router as Router;
    

   class ObjForm extends RoutingObj {

        VAR $_CONN_ID;
        VAR $authroles;
        VAR $_ROUTE_PARAMS;
        VAR $_INFO;

        function __construct($_req_params,$cfg_info=[],$pmodule=null)
        {
            parent::__construct($_req_params,$cfg_info,$pmodule);
            $this->_ROUTE_PARAMS = $_req_params;
            $this->OnLoad();
        }

        function load_me()
        {

        }

        function OnLoad()
        {
            $_cfg_info = $this->P_MODULE->exe_mod_func('restbox', 'get_settings');
		//	print_dbg($_cfg_info ) ;
			$form_cfg = url_seg_add($_cfg_info['CFG_DIR'],'forms',$this->_ROUTE_PARAMS['object']['name']).".php";
			if(!file_exists($form_cfg))
			{
				$this->P_MODULE->exe_mod_func('restbox','out_error',['message'=>"Form {$this->_ROUTE_PARAMS['object']['name']} not exists",'errno'=>54]);
				return;
			}
		//	$obj_class_name = $this->_obj_map[$this->_ROUTE_PARAMS['obj_class']];
			
            include $form_cfg;
            $this->_INFO = $info;
         //   print_dbg($this->_INFO);
		//	$form_obj = new $obj_class_name($_cfg_info);
        }

        static function GetRoutePatterns()
        {
            return [
                    'forms/:table:'=>'view',
                ];
        }

        function AInfo()
        {
        //    print_dbg($this->_INFO->_info);
            if(isset($this->_INFO->_info['fields'])) 
            {
                return $this->_INFO->_info['fields'];
            }
        }

        function ASubmit($data=null)
        {
            if($data==null)
            {
                $data=$_POST;
            }

            if(isset($this->_INFO['_info']['events']['OnSubmit'])) 
            {
                return $this->_INFO['_info']['events']['OnSubmit']($data);
            }
        }

        static function FindPattern($req_str,$ptrn_list)
        {
        //    print_dbg($req_str);

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

        function connect_db($dbparams)  // connect the database
        {
         
       //     print_dbg( 'exists: '.$this->call_mod_func('restbox.db','connection_exists',$this->_CONN_ID) );
            if(! $this->P_MODULE->call_mod_func('restbox.db','connection_exists',$this->_CONN_ID) )
            {
                
                $this->P_MODULE->call_mod_func('restbox.db','connect',$dbparams,$this->_CONN_ID);    
            }
        }

        static function GetDefAction()
        {
            return "AInfo";
        }
        
        function beforeAction($_req_params)
        {
            $rb_info = $this->P_MODULE->exe_mod_func('restbox','get_settings');

            def_options(['conn_id'=>0],$_req_params['vars']);
            
            $this->_CONN_ID = $_req_params['vars']['conn_id'];
            $_db_info = $rb_info['connections'][$this->_CONN_ID];
         //   print_dbg('connectingg');

            $this->connect_db($_db_info);
        }


       
        
   }

}

