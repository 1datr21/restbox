<?php 
namespace modules\restbox\table {

    class ft_bool extends fieldttype_base
    {
        function OnCreate_std($_args)
        {
            /*
           ALTER TABLE `tms_tasks` ADD `active` BOOLEAN NOT NULL AFTER `options`;
            
            */
            $seg = "`{$this->fldname}` {$this->PARAMS['_type']} NOT NULL DEFAULT '".($this->PARAMS['default'] ? 1 : 0)."'";
            return [                
                'fld_seg'=>$seg,
                'add_queries'=>[]
            ];
        }

        function OnConstruct(&$params_)
        {
            def_options(['_type'=>'BOOLEAN','default'=>false],$params_);
        }
    }

}
?>