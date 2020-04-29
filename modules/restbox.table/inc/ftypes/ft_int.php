<?php 
namespace modules\restbox\table {

    class ft_int extends fieldttype_base
    {
        function OnCreateTable_std($_args)
        {
            /*
            ALTER TABLE `tms_users` CHANGE `age` `age` BIGINT(20) UNSIGNED NOT NULL;
            
            */
            $attr_str=($this->PARAMS['unsigned'] ? "UNSIGNED" : "");
            return [                
                'fld_seg'=>"`{$this->fldname}` {$this->PARAMS['_type']} $attr_str NOT NULL ",
                'add_queries'=>[]
            ];
        }

        function OnConstruct(&$params_)
        {
            def_options(['_type'=>'bigint','_size'=>20,'unsigned'=>false],$params_);
        }

        function validate($_a_value)
        {
            if(empty($_a_value))
            {
                return "Id could not be empty";
            }
            return null;
        }
    }

}
?>