<?php

namespace modules\restbox\table {

    use modules\restbox\table\fieldttype_base as fieldttype_base;

    class ft_enum extends fieldttype_base
    {
     
        function OnConstruct(&$params_)
        {
            def_options(['values'=>['item1']],$params_);
        }

        function OnCreateTable_std($_args)
        {
            $seg = "`{$this->fldname}` ENUM(".implode(',',transform_array( $this->PARAMS['values'] ,"'{%val}'")).")  {$this->str_required()}";
            if(isset($this->PARAMS['default']))
            {
                $seg = $seg." DEFAULT '{$this->PARAMS['default']}'";
            }
            return [
               	// ALTER TABLE `tms_users` CHANGE `status` `status` ENUM('val1','','val2','val3') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'val1';
                'fld_seg'=>$seg,
                'add_queries'=>[]
            ];
        }

        

        function ValueList()
        {
            return  $this->PARAMS['values'];
        }
    }


}
?>