<?php
namespace modules\restbox\table {
    use modules\restbox as restbox;
    use modules\restbox\rbEnv as rbEnv;
    

   class ObjTable extends restbox\AppObject {
   
        VAR $_CONN_ID;
        VAR $_TABLE;

        function __construct($_req_params,$cfg_info=[],$pmodule=null)
        {
           parent::__construct($_req_params,$cfg_info,$pmodule);
        }

        static function getKey($_req_params) // key to object map
        {
         //   print_dbg($_req_params);

            return "tables/".$_req_params['vars']['table'];
        }

        static function GetRoutePatterns($mode=1)
        {
            $ptrns = [
                'tables/:table:'=>'view',
                'tables/one/:table:/:id:'=>'item',                
                'tables/delete/:table:'=>'delete',                
            ];
            if($mode==2)
            {
                $ptrns['tables/:table:']='tform';
                $ptrns['tables/:table:/[:id:]']='tform';
                $ptrns['tables/save/:table:']='save';
                $ptrns['tables/:table:/newrow']='save';
            }
            return $ptrns;
        }

       

        function tform($_request)
        {
            include "/std/all/forms/formtable.php";
            //print_dbg($info);
            return $info;
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

        function build_info($info,$tname)
        {
            
            $res = new TableMap($tname,$info); // table map from description
            foreach($info->_info['fields'] as $fld => $fldinfo)
            {
                //$this->call_mod_func('')
               $fld_obj = $this->P_MODULE->load_ftype($fld,$fldinfo);
               $res->add_field($fld,$fld_obj);
            }
            return $res;
        }

        function view($_request)  
        {
            $info_obj = $this->P_MODULE->load_table($_request['vars']['table']);
            
            $rbenv = new rbEnv($this->P_MODULE);
            $do_it = true;

            if(isset($info_obj->_info['events']['onAccess']))
            {
                $info_obj->_info['events']['onAccess']($_request, $rbenv, $do_it);
            } 

            //print_dbg($info_obj);
            if($do_it)
            {
                return $this->call_mod_func('restbox.db', 'query_select',[ 'table'=> $_request['vars']['table'], '#table_params'=>$info_obj]);
            }
            else
            {
                $this->call_mod_func('restbox','out_error','Error 403 Access forbidden');
            }

            return null;            
        }

        function delete($_request, $_post_data=[])
        {
            $_post_data=$_POST;
            $info_obj = $this->P_MODULE->load_table($_request['vars']['table']);

            $rbenv = new rbEnv($this->P_MODULE);

            $do_it = true;
            if(isset($info_obj->_info['events']['onAccess']))
            {
                $info_obj->_info['events']['onAccess']($_request, $rbenv, $do_it);
            } 

            if($do_it)
            {
                $id_fld = $info_obj->get_id_field();
                $this->call_mod_func('restbox.db', 
                    'query_delete',
                    $_request['vars']['table'],
                    $id_fld->fldname.'='.$_POST[$id_fld->fldname]
                );
            }
            else
            {
                $this->call_mod_func('restbox','out_error','Error 403 Access forbidden');
            }
             
            return [null];
        }

        function item($_request)
        {
            $info_obj = $this->P_MODULE->load_table($_request['vars']['table']);
            $id_fld_name = $info_obj->get_id_field();
        //  get an item
            $rows=[];
            
            $rows[]=$this->load_by_id($_request['vars']['table'], $_request['vars']['id']);
            
            return $rows;    
        }

        function load_by_id($_table,$_id_val)
        {
            $res = $this->call_mod_func('restbox.db', 'query',"SELECT * FROM `@+{$_table}` WHERE id={$_id_val}");
            if($row = $this->call_mod_func('restbox.db', 'fetch_object',$res))
            {
                return $row;
            }   
            return null;
        }

        function save($_request,$_post_data=[])
        {  
         //   print_dbg('+++ saving +++');
            $_post_data=$_POST;
            $info_obj = $this->P_MODULE->load_table($_request['vars']['table']);

            $rbenv = new rbEnv($this->P_MODULE);
            // call event to access
            $do_it = true;
            if(isset($info_obj->_info['events']['onAccess']))
            {
                $info_obj->_info['events']['onAccess']($_request, $rbenv, $do_it);
            }             

            if(!$do_it)
            {
                $this->call_mod_func('restbox','out_error',['message'=>'Error 403 Access forbidden']);
                return false;
            }           

            $arr_to_save=[];
            $ID_fld = $info_obj->get_id_field();
            if(!empty($_post_data[$ID_fld->fldname]))
            {
                // load by id
                $item = $this->load_by_id($_request['vars']['table'], $_post_data[$ID_fld->fldname]);
                foreach($info_obj->FIELDS as $fld)
                {
                    if($fld->fldname==$ID_fld->fldname) continue;  
                    if(isset($_post_data[$fld->fldname]))
                    {                      
                        $item[$fld->fldname]=$_post_data[$fld->fldname];
                    }
                }
              
                //execute event Before Save
                $save_it = true;
                if(isset($info_obj->_info['events']['beforeSave']))
                {
                    $info_obj->_info['events']['beforeSave']($item, $rbenv, $save_it);
                } 
                if($save_it)
                {
                    // Event before insert
                    foreach($info_obj->FIELDS as $fld)
                    {
                        $_params = ['datarow'=>&$item];
                        $fld->BeforeUpdate($_params);
                    }

                    $res = $this->call_mod_func('restbox.db', 'query_update',[
                        'item'=>$item,
                        'table'=>$_request['vars']['table'],
                        'idval'=>$_post_data[$ID_fld->fldname],
                        'idfld'=>$ID_fld->fldname,
                        ]);
                    //execute event After Save
                    if(isset($info_obj->_info['events']['afterSave']))
                    {
                        $info_obj->_info['events']['afterSave']($item);
                    }
                }
            }
            else
            {
                foreach($info_obj->FIELDS as $fld)
                {
                    if($fld->fldname==$ID_fld->fldname) continue;
                    $item[$fld->fldname]=$_post_data[$fld->fldname];
                }

                //execute event Before Save
                $save_it = true;
                if(isset($info_obj->_info['events']['beforeSave']))
                {
                    $info_obj->_info['events']['beforeSave']($item, $rbenv, $save_it);
                }               
                if($save_it)
                {
                     // Event before insert
                    foreach($info_obj->FIELDS as $fld)
                    {
                        $_params = ['datarow'=>&$item];
                        $fld->BeforeInsert($_params);
                   //     print_dbg($fld);
                    }
                    $res = $this->call_mod_func('restbox.db', 'query_insert',['item'=>$item,'table'=>$_request['vars']['table'],'#table_params'=>$info_obj]);
                    //execute event After Save
                    if(isset($info_obj->_info['events']['afterSave']))
                    {
                        $info_obj->_info['events']['afterSave']($item,$rbenv);
                    }

                    return $res;
                }
            }     
            return true;
        }

   }

    class TableMap {

        VAR $TNAME;
        VAR $_P_MODULE;
        VAR $FIELDS = [];

        function __construct($tbl_name,$_info_=[],$_P_MODULE=null)
        {
            $this->TNAME = $tbl_name;
            $this->_P_MODULE = $_P_MODULE;
           // print_dbg($_info_);

            if(is_object($_info_))
            {
            //    print_dbg($_info_->_info);

                def_options(['onAccess'=>function($request,&$rbenv,&$do_it)
                {
                //    print_dbg('def event works');
                    if(($request['path']=='tables/save') || ($request['path']=='tables/delete'))
                    {            
                    //    print_dbg($rbenv->exe_mod_func('restbox.session','sess_vars'));
                        if(!$rbenv->exe_mod_func('restbox.session','var_exists','user_id'))
                        {
                            $do_it=false;
                        }
                    }
                }],$_info_->_info['events']);   
               // print_dbg('set the def event');
            }

            foreach($_info_ as $fld => $val)
            {
                if(!in_array($fld,['FIELDS']))
                {
                    $this->$fld = $val;
                }
            }

            //print_dbg();
        }

        function getName()
        {
            return $this->TNAME;
        }

        function add_field($fldname,$finfo)
        {
            $this->FIELDS[$fldname] = $finfo;
        }

        public function validate($_data,$with_id=false)
        {
            function exe_validate($fld,$fld_obj,$_data,&$res_arr)
            {
                $res = $fld_obj->validate($_data[$fld]);
                if($res!=null)
                {
                    $res_arr[$fld]=$res;
                }
            }

            $res_arr=[];
            foreach($this->FIELDS as $fld => $fld_obj)
            {
                if($fld_obj->isID())
                {
                    if($with_id)
                    {
                        exe_validate($fld,$fld_obj,$_data,$res_arr);
                    }
                }
                else
                {
                    exe_validate($fld,$fld_obj,$_data,$res_arr);
                }
            }
            return $res_arr;
        }

        public function save($_post_data=[])
        {
          //  $this->call_obj('tables/'.$this->getName().'','modules\restbox\table\ObjTable',2);
            $newobj = $this->_P_MODULE->exe_mod_func('restbox.table','save',$this->getName(),$_post_data);
            return $newobj;
        }

        public function get_item($_id_val)
        {
            $id_fld_name = $this->get_id_field();
            //print_dbg($id_fld_name);
            $res = $this->_P_MODULE->exe_mod_func('restbox.db', 'query',"SELECT * FROM `@+{$this->TNAME}` WHERE {$id_fld_name->fldname}={$_id_val}");
            if($row = $this->_P_MODULE->exe_mod_func('restbox.db', 'fetch_object',$res))
            {
             //   return $row;
                $res = [];
                foreach($this->FIELDS as $fld => $fld_obj)
                {
                //  print_dbg($fld_obj);
                    if(!$fld_obj->isID())
                    {
                        $arr = ['defval' => $row[$fld] ];
                        $vl = $fld_obj->ValueList();
                        if(!empty($vl))
                        {
                            $arr['valuelist']=$vl;
                        }
                        $res[$fld] = $arr; 

                    }
                    else
                    {
                        $res[$fld] = ['defval' => $row[$fld] ];                        
                    }
                } 
                return $res;
            }   
            return null;
        }

        function OnDropField()
        {
            $id_fld_name = $this->get_id_field();
            $res = $this->_P_MODULE->exe_mod_func('restbox.db', 'query',"SELECT * FROM `@+{$_table}` WHERE {$id_fld_name}={$_id_val}");
            if($row = $this->_P_MODULE->exe_mod_func('restbox.db', 'fetch_object',$res))
            {
                return $row;
            }   
            return null;
        }

        function get_new_row()
        {
            $res=[];
            foreach($this->FIELDS as $fld => $fld_obj)
            {
             //  print_dbg($fld_obj);
                if(!$fld_obj->isID())
                {
                    $arr = ['defval' => $fld_obj->getDefault() ];
                    $vl = $fld_obj->ValueList();
                    if(!empty($vl))
                    {
                        $arr['valuelist']=$vl;
                    }
                    $res[$fld] = $arr; 

                }
            } 
            return $res;
        }

        function get_id_field()
        {
            foreach($this->FIELDS as $fld => $fld_obj)
            {
                if($fld_obj->isID())
                {
                    return $fld_obj;
                }
            } 
        }

        function get_need_fields()
        {
            $res_arr=[];
            foreach($this->FIELDS as $fld => $fld_obj)
            {
                $flds = $fld_obj->get_fields();
                foreach($flds as $fld)
                {
                    $res_arr[]=$fld;
                }
            }
            return $res_arr;
        }
    }
}