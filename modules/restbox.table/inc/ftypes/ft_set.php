<?php

namespace modules\restbox\table {

    use modules\restbox\table\fieldttype_base as fieldttype_base;

    class ft_set extends fieldttype_base
    {
     
        function OnConstruct(&$params_)
        {
            def_options(['values'=>['item1']],$params_);
        }

        function OnCreateTable_std($_args)
        {
            $seg = "`{$this->fldname}` SET(".implode(',',transform_array( $this->PARAMS['values'] ,"'{%val}'")).") NOT NULL";
            if(isset($this->PARAMS['default']))
            {
                if(is_array($this->PARAMS['default']))
                    $_def_str = implode(',',$this->PARAMS['default']);
                else
                    $_def_str = $this->PARAMS['default'];
                $seg = $seg." DEFAULT '{$_def_str}'";
            }
            return [
               	// options` SET('send_pm','notify_mail','respect','') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'send_pm,respect';
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
