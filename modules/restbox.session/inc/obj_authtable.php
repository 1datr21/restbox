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
       //     print_dbg($post_data);
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
                    $_login_or_email = isset($post_data['login']) ? $post_data['login'] : $post_data[$this->authroles['login']];
                   // $_email = isset($post_data['email']) ? $post_data['email'] : $post_data[$this->authroles['email']];
                    $where = $this->authroles['login']."='{$_login_or_email}' OR {$this->authroles['email']}='$_login_or_email'";
                };break;
            }
        //    print_dbg($where);
            $query_res = $this->call_mod_func('restbox.db', 'query_select',[ 
                'table'=> $_request['vars']['table'], 
                'where'=> $where,
                '#table_params'=>$table_info
                ]);

            $_login_err_text = 'Wrong login/e-mail or password';
            if($query_res['total_count']==0)
            {
                $this->call_mod_func('restbox','out_error',['mess'=>$_login_err_text]);
            }

         //   print_dbg($table_info->FIELDS);

            $_password = isset($post_data['password']) ? $post_data['password'] : $post_data[$this->authroles['password']];
            $passw_cmp = $table_info->FIELDS[$this->authroles['password']]->compare_password(
                $query_res['items'][0],
                $_password);

            if(!$passw_cmp)
            {
                $this->call_mod_func('restbox','out_error',['mess'=>$_login_err_text]);
            }
            return ['success'=>true,'SESS_ID'=> $this->P_MODULE->start_session()];  
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
