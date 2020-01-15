<?php 
namespace modules\restbox\table {

    class ft_ref extends fieldttype_base
    {
        VAR $tbl_nested;

        function OnCreateTable_std($_args)
        {
            /*
            ALTER TABLE `tms_users` CHANGE `age` `age` BIGINT(20) UNSIGNED NOT NULL;

            ALTER TABLE `tms_tasks`	ADD CONSTRAINT `FK_tms_tasks_tms_users` FOREIGN KEY (`author`) REFERENCES `tms_users` (`id`);
            
            */
            $attr_str=($this->PARAMS['unsigned'] ? "UNSIGNED" : "");
            return [                
                'fld_seg'=>"`{$this->fldname}` {$this->PARAMS['fld_type']} $attr_str NOT NULL ",
                'add_queries'=>[]
            ];
        }

        function OnConstruct(&$params_)
        {
         //   def_options(['_type'=>'bigint','_size'=>20,'unsigned'=>false],$params_);
            //берем тип из таблицы
        }

        function AfterConstruct()
        {

            $_table_info = $this->_P_MODULE->load_table($this->PARAMS['table']);
            //print_dbg($_table_info);

            if(!empty($_table_info))
            {
                $id_fld = $_table_info->get_id_field();
                //print_dbg($id_fld);

                if(!empty($id_fld))
                {
                    def_options($id_fld->PARAMS, $this->PARAMS,['fld_type','size']);
                    //$this->PARAMS=$id_fld->PARAMS;
                }
                
                // get the field of id and it's type
            }
        }
    }
}
?>