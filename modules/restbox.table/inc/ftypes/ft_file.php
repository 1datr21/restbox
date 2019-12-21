<?php 
namespace modules\restbox\table {

    class ft_file extends fieldttype_base
    {
        function OnCreate_std($_args)
        {
            return [
                'fld_seg'=>"`{$this->fldname}` text NOT NULL ",
                'add_queries'=>[]
            ];
        }
    }

}
?>