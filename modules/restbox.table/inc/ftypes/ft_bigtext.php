<?php 
namespace modules\restbox\table {

    class ft_bigtext extends fieldttype_base
    {
        function OnCreateTable_std($_args)
        {
            return [
                'fld_seg'=>"`{$this->fldname}` longtext NOT NULL ",
                'add_queries'=>[]
            ];
        }
    }

}
?>