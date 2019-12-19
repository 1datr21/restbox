<?php
namespace modules\restbox\table {

    class ft_password extends fieldttype_base
    {
        function OnConstruct(&$params_)
        {
            def_options(['length'=>6, 'method'=>'md5'],$params_);
        }

        function OnCreate_std($_args)
        {
            return [
                'fld_seg'=>"`{$this->fldname}` text NOT NULL ",
                'add_queries'=>[]
            ];
        }
    }

}