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
            def_options(['mode'=>'full'],$this->authroles);
            if(isset($table_info->_info['addinfo']['authroles']))
            {
                $this->authroles = $table_info->_info['addinfo']['authroles'];
            }
            
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
            if($this->authroles['mode']=='full')
            {
                $_login = isset($post_data['login']) ? $post_data['login'] : $post_data[$this->authroles['login']];
                $_email = isset($post_data['email']) ? $post_data['email'] : $post_data[$this->authroles['email']];
                $where = $this->authroles['login']."='{$_login}' OR {$this->authroles['email']}=$_email";
            }
            $query_res = $this->call_mod_func('restbox.db', 'query_select',[ 
                'table'=> $_request['vars']['table'], 
                'where'=> $where,
                '#table_params'=>$table_info
                ]);

            print_dbg($query_res);
                
            return ['table'=>$_request['vars']['table']];  
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
