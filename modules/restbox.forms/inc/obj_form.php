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
          //  print_dbg($_cfg_info ) ;
            $form_cfg = url_seg_add($_cfg_info['CFG_DIR'],$_cfg_info['_EP'],'forms',$this->_ROUTE_PARAMS['object']['name']).".php";
 
			if(!file_exists($form_cfg))
			{
                $form_info = $this->P_MODULE->call_form_info($this->_ROUTE_PARAMS);
         //       print_dbg(">>");
        //   print_dbg($form_info);
                if($form_info!=null)
                {
                    if(is_array($form_info))
                    {
                        if(isset($form_info['form_cfg_file']))
                        {
                            $form_cfg = $form_info['form_cfg_file'];
                            include $form_cfg;
                        //    $this->_INFO = $info;
                            $this->init_form_info($info);
                            return;
                        }
                    }
                    else 
                    {
                    //    print_dbg("@@>");                       
                        $this->init_form_info($form_info);
                        return;
                    }
                }
                
                $this->P_MODULE->exe_mod_func('restbox','out_error',['message'=>"Form {$this->_ROUTE_PARAMS['object']['name']} not exists",'errno'=>54]);
                return;	
            }
            else
            {
                include $form_cfg;
                $this->init_form_info($info);
                //$this->_INFO = $info;
            } 		
            
        }

        function init_form_info($finfo)
        {
            $this->_INFO = $finfo;
            $this->_INFO->set_p_module($this);
        }

        static function GetRoutePatterns()
        {
            return [
                    'forms/:table:'=>'view',
                ];
        }

        function AInfo()
        {
            $finfo=[];

            // check access
        /*    if(isset($this->_INFO->_info['events']['OnAccess'])) 
            {
                $res = $this->_INFO->_info['events']['OnAccess']($this, []);

                if(!is_null($res))
                {
                    $this->P_MODULE->exe_mod_func('restbox','out_error',['message'=>"Access forbidden",'errno'=>403]);
                    return; 
                } 
            }*/

            if(isset($this->_INFO->_info['fields'])) 
            {
                $finfo['fields'] = $this->_INFO->_info['fields'];
            }

            $finfo['csrf'] = $this->gen_token();
            if(isset($this->_INFO->_info['events']['OnInit']))   
            {
                $finfo['addinfo'] = $this->_INFO->_info['events']['OnInit']($this);
            }
            return $finfo;
        }

        function AValidate($data=null)
        {
            if($data==null)
            {
                $data=$_POST;
            }
            // check csrf
            $csrf_key = '';
            if(!$this->check_csrf($data,$csrf_key,false))
            {
                $this->P_MODULE->exe_mod_func('restbox','out_error',['message'=>"Access forbidden",'errno'=>403]);
                return;    
            }  

            // check access
            if(isset($this->_INFO->_info['events']['OnAccess'])) 
            {
                $res = $this->_INFO->_info['events']['OnAccess']($this, []);

                if(!is_null($res))
                {
                    $this->P_MODULE->exe_mod_func('restbox','out_error',['message'=>"Access forbidden",'errno'=>403]);
                    return; 
                }
            }

            $ftokens = $this->call_mod_func('restbox.session','get_var','FORM_TOKENS',[]);
            
            $ftokens[$csrf_key]['validated'] = true;
            
            $this->call_mod_func('restbox.session','set_var','FORM_TOKENS',$ftokens);
                        
            if(isset($this->_INFO->_info['events']['OnValidate']))   
            {
                return $this->_INFO->_info['events']['OnValidate']($this, $data);
            }
        }

        function ASubmit($data=null)
        {
            //print_dbg("ASubmit");
            if($data==null)
            {
                $data=$_POST;
            }
        //    print_dbg($this->_INFO->_info['events']);
            // check csrf
            $csrf_key = '';
            if(!$this->check_csrf($data,$csrf_key,false))
            {
                $this->P_MODULE->exe_mod_func('restbox','out_error',['message'=>"Access forbidden",'errno'=>403]);
                return;    
            }            

            // check access
            if(isset($this->_INFO->_info['events']['OnAccess'])) 
            {
                $res = $this->_INFO->_info['events']['OnAccess']($this, []);

                if(!is_null($res))
                {
                    $this->P_MODULE->exe_mod_func('restbox','out_error',['message'=>"Access forbidden",'errno'=>403]);
                    return; 
                }
            }

            $ftokens = $this->call_mod_func('restbox.session','get_var','FORM_TOKENS',[]);
            
        //    print_dbg($ftokens);
        //    print_dbg($csrf_key);
            if(!$ftokens[$csrf_key]['validated'])
            {
                $this->P_MODULE->exe_mod_func('restbox','out_error',['message'=>"Access forbidden",'errno'=>403]);
                return null;
            }

            if(isset($this->_INFO->_info['events']['OnSubmit'])) 
            {
                $res = $this->_INFO->_info['events']['OnSubmit']($this, $data);

                $this->change_token($csrf_key);
            }
        }       
        
        function change_token($token_old)
        {
          
            $newtoken = $this->gen_token();
            $ftokens = $this->call_mod_func('restbox.session','get_var','FORM_TOKENS',[]);
            unset($ftokens[$token_old]);
            $ftokens[$newtoken['csrf_id']] = ['token'=>$newtoken['csrf_val'],'_time'=>time(),'validated'=>false];// [ $newtoken['csrf_val']];
            $this->call_mod_func('restbox.session','set_var','FORM_TOKENS',$ftokens);

            $changed = $this->call_mod_func('restbox','get_ext_data','csrf_changed',[]);
            $changed[]=[ 
                'token_old'=>$token_old, 
                'token_new'=>$newtoken['csrf_id'], 
                'token_new_val'=>$newtoken['csrf_val'],
            ];
            $this->call_mod_func('restbox','add_ext_data','csrf_changed',$changed );
        }

        function delete_token($token)
        {

        }

        function check_csrf($formdata,&$curr_key,$delete_token=true)
        {
          //  print_dbg('check csrf');
            $ftokens = $this->get_token_list();
         //   print_dbg($ftokens);
            foreach($ftokens as $tkey => $tval)
            {
                if(isset($formdata[$tkey]))
                {                    
                    $curr_key = $tkey;
                    $res = ($formdata[$tkey]==$tval['token']);
                    if($res && $delete_token)
                    {
                        unset($ftokens[$tkey]);  
                        $this->call_mod_func('restbox.session','set_var','FORM_TOKENS',$ftokens);  
                    }
                    return $res;
                }
            }
            return false;
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

        function get_token_list()
        {
            $ftokens = $this->call_mod_func('restbox.session','get_var','FORM_TOKENS',[]);
            $token_exp_time = 5000;
            $save=false;
         
            foreach($ftokens as $tkey => $tinfo)
            {
            //    print_dbg($tinfo);
                if(time()-$tinfo['_time'] > $token_exp_time)
                {
                    unset($ftokens[$tkey]);
                    $save=true;
                }
            }
            if($save)
            {
                $this->call_mod_func('restbox.session','set_var','FORM_TOKENS',$ftokens);
            }
            return $ftokens;
        }

        function gen_token()
        {
            $ftokens = $this->get_token_list();
            // gen the id
            do {
                $csrf_id = GenRandStr(10);
            }
            while(isset($ftokens[$csrf_id]));

            $csrf_val = GenRandStr(25);
            $ftokens[$csrf_id]=['token'=>$csrf_val,'_time'=>time(),'validated'=>false];
        //    print_dbg('ftokens');
        //    print_dbg($ftokens);
            $this->call_mod_func('restbox.session','set_var','FORM_TOKENS',$ftokens);

            return ['csrf_id'=>$csrf_id,'csrf_val'=>$csrf_val];
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

