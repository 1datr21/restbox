<?php 
namespace modules\restbox\table {

    class ft_bool extends fieldttype_base
    {
        function OnCreateTable_std($_args)
        {
            /*
           ALTER TABLE `tms_tasks` ADD `active` BOOLEAN NOT NULL AFTER `options`;
            
            */
            $seg = "`{$this->fldname}` {$this->PARAMS['_type']}  {$this->str_required()} DEFAULT '".($this->PARAMS['default'] ? 1 : 0)."'";
            return [                
                'fld_seg'=>$seg,
                'add_queries'=>[]
            ];
        }

        function OnConstruct(&$params_)
        {
            def_options(['_type'=>'BOOLEAN','default'=>0],$params_);
        }

        function def_params()
        {
            return ['require'=>false,'default'=>0];
        }

        function get_default()
        {
            

            if(isset($this->PARAMS['default']))
            {
            //    print_dbg($this->PARAMS);
            //    print_dbg(':::');
                return (string)$this->PARAMS['default'];
            }
            return null;
        }
    }

}
?>