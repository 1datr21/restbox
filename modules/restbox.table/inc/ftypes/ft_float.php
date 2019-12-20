<?php 
namespace modules\restbox\table {

    class ft_float extends fieldttype_base
    {
        function OnCreate_std($_args)
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
            def_options(['_type'=>'double','_size'=>20,'unsigned'=>false],$params_);
        }
    }

}
?>
