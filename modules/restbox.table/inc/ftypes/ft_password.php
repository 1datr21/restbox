<?php
namespace modules\restbox\table {

    class ft_password extends fieldttype_base
    {
        function OnConstruct(&$params_)
        {
            def_options(['length'=>6, 'method'=>'md5'],$params_);
        }
    }

}