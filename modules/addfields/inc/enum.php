<?php

namespace modules\addfields {

    use modules\restbox\table\fieldttype_base as fieldttype_base;

    class ft_enum extends fieldttype_base
    {
     
        function OnConstruct($params_)
        {
            def_options(['values'=>['item1']],$params_);
        }
    }


}
?>