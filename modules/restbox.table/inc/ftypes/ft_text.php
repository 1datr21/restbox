<?php 
namespace modules\restbox\table {

    class ft_text extends fieldttype_base
    {
        function OnCreateTable_std($_args)
        {
            return [
                'fld_seg'=>"`{$this->fldname}` text {$this->str_required()} ",
                'add_queries'=>[]
            ];
        }
    }

}
?>