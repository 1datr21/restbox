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
                    'tables/one/:table:/:id:'=>'item',
                    'tables/save/:table:'=>'save',
                    'tables/delete/:table:'=>'delete',
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
            //print_dbg($info_obj);
            return $this->call_mod_func('restbox.db', 'query_select',[ 'table'=> $_request['vars']['table'], '#table_params'=>$info_obj]);
            
        }

        function delete($_request, $_post_data=[])
        {
            $_post_data=$_POST;
            $info_obj = $this->P_MODULE->load_table($_request['vars']['table']);
            $id_fld = $info_obj->get_id_field();
            $this->call_mod_func('restbox.db', 
                'query_delete',
                $_request['vars']['table'],
                $id_fld->fldname.'='.$_POST[$id_fld->fldname]
        );
             
            return [null];
        }

        function item($_request)
        {
            //include $this->CFG_INFO['CFG_DIR']."/tables/".$_request['vars']['table'].".php";

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
            $_post_data=$_POST;
            $info_obj = $this->P_MODULE->load_table($_request['vars']['table']);
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
                    $info_obj->_info['events']['beforeSave']($item, $save_it);
                } 
                if($save_it)
                {
                    // Event before insert
                    foreach($info_obj->FIELDS as $fld)
                    {
                        $_params = ['datarow'=>&$item];
                        $fld->BeforeInsert($_params);
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
                    $info_obj->_info['events']['beforeSave']($item, $save_it);
                }               
                if($save_it)
                {
                     // Event before insert
                    foreach($info_obj->FIELDS as $fld)
                    {
                        $_params = ['datarow'=>&$item];
                        $fld->BeforeInsert($_params);
                    }
                    $res = $this->call_mod_func('restbox.db', 'query_insert',['item'=>$item,'table'=>$_request['vars']['table']]);
                    //execute event After Save
                    if(isset($info_obj->_info['events']['afterSave']))
                    {
                        $info_obj->_info['events']['afterSave']($item);
                    }
                }
            }     
            return true;
        }

   }

    class TableMap {

        VAR $TNAME;
        VAR $FIELDS = [];

        function __construct($tbl_name,$_info_=[])
        {
            $this->TNAME = $tbl_name;
            foreach($_info_ as $fld => $val)
            {
                if(!in_array($fld,['FIELDS']))
                {
                    $this->$fld = $val;
                }
            }
        }

        function getName()
        {
            return $this->TNAME;
        }

        function add_field($fldname,$finfo)
        {
            $this->FIELDS[$fldname] = $finfo;
        }

        function OnDropField()
        {
            
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