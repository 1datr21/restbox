<?php 
namespace modules\restbox\table {

    class ft_datetime extends fieldttype_base
    {
        function OnCreate_std($_args)
        {
            /*
            ALTER TABLE `tms_users` ADD `dtt` DATETIME NOT NULL AFTER `money`;            
            */
            $attr_str=($this->PARAMS['unsigned'] ? "UNSIGNED" : "");
            $_type = ucwords($this->PARAMS['mode']);
            return [                
                'fld_seg'=>"`{$this->fldname}` {$_type} $attr_str NOT NULL ",
                'add_queries'=>[]
            ];
        }

        function OnConstruct(&$params_)
        {
            // mode : date, datetime, time, timestamp
            def_options(['mode'=>'date','unsigned'=>false],$params_);
        }
    }

}
?>
