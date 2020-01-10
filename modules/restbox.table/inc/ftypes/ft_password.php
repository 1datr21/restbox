<?php
namespace modules\restbox\table {

    class ft_password extends fieldttype_base
    {
        function OnConstruct(&$params_)
        {
            def_options(['length'=>6, 'method'=>'md5'],$params_);
        }

        function OnCreateTable_std($_args)
        {
            return [
                'fld_seg'=>"`{$this->fldname}` text NOT NULL ",
                'add_queries'=>[]
            ];
        }

        function BeforeInsert(&$item)
        {
            if($this->PARAMS['mode']=='md5')
            {
                $item['datarow'][$this->fldname] = md5($item['datarow'][$this->fldname]) ;
            }
            //print_dbg($item);
        }

        function BeforeUpdate(&$item)
        {
            if($this->PARAMS['mode']=='md5')
            {
                $item['datarow'][$this->fldname] = md5($item['datarow'][$this->fldname]) ;
            }
        }
    }

}