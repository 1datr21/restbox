<?php
namespace modules\restbox\session {
    use modules\restbox as restbox;
    

   class ObjAuthTable extends restbox\AppObject {

        VAR $_CONN_ID;
        VAR $authroles;

        function __construct($_req_params,$cfg_info=[],$pmodule=null)
        {
            parent::__construct($_req_params,$cfg_info,$pmodule);
        }

        static function GetRoutePatterns()
        {
            return [
                    'auth/[:table:]'=>'auth',
                    'auth/logout'=>'logout',
                ];
        }

        function auth($_request,$post_data=[])
        {
            $post_data=$_POST;
        //  get auth parameters
            $info_cfg = $this->call_mod_func('restbox','get_settings',1);
            if(empty($_request['vars']['table']))
            {
                
                if(is_array($info_cfg['usertable']))
                {
                    $_request['vars']['table'] = $info_cfg['usertable'][0];
                }
                else
                {                
                    $_request['vars']['table'] = $info_cfg['usertable'];
                }
            }
        //    print_dbg($_request);
            $table_info = $this->call_mod_func('restbox.table', 'load_table', $_request['vars']['table']);
                    
            
            if(isset($table_info->_info['addinfo']['authroles']))
            {
                $this->authroles = $table_info->_info['addinfo']['authroles'];
            }
            def_options(['mode'=>'full'],$this->authroles);
            
            if(!isset($this->authroles['login']))
            {
                $this->authroles['login'] = $this->search_fld_by_synonims($table_info->_info['fields'],'login');
            }

            if(!isset($this->authroles['password']))
            {
                $this->authroles['password'] = $this->search_fld_by_synonims($table_info->_info['fields'],['password','passw']);
            }

            if(!isset($this->authroles['email']))
            {
                $this->authroles['email'] = $this->search_fld_by_synonims($table_info->_info['fields'],['email','e_mail','e-mail']);
            }
            //
         //   print_dbg($this->authroles);
            $where='';
            switch($this->authroles['mode'])
            {
                case 'full':
                {
                    $_login = isset($post_data['login']) ? $post_data['login'] : $post_data[$this->authroles['login']];
                    $_email = isset($post_data['email']) ? $post_data['email'] : $post_data[$this->authroles['email']];
                    $where = $this->authroles['login']."='{$_login}' OR {$this->authroles['email']}='$_email'";
                };break;
            }
        //    print_dbg($where);
            $query_res = $this->call_mod_func('restbox.db', 'query_select',[ 
                'table'=> $_request['vars']['table'], 
                'where'=> $where,
                '#table_params'=>$table_info
                ]);

            if($query_res['total_count']==0)
            {
                $this->call_mod_func('restbox','out_error',['mess'=>'Wrong login/e-mail or password ']);
            }
            print_dbg($query_res);

            return ['table'=>$_request['vars']['table']];  
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
         //   print_dbg('connectingg');

            $this->connect_db($_db_info);
        }


        function search_fld_by_synonims($fldbuf,$syn_array)
        {
            if(!is_array($syn_array))
            {
                $syn_array = [$syn_array];
            }
            foreach($fldbuf as $_fld_name => $fld)
            {
                foreach($syn_array as $syn)
                {
                    if(stristr($_fld_name,$syn)!=FALSE)
                    {
                        return $_fld_name;
                    }
                }
            }
            return null;
        }

        function logout()
        {
            $this->P_MODULE->clear_session();
        }
   }

}
