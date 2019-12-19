<?php 
namespace modules\restbox\table {
    class ft_id extends fieldttype_base
    {
       
        function __construct($params=[],$_fldname='id')
        {
            parent::__construct($params,$_fldname);
        }

        function OnCreate_std($_args)
        {
            return [
                'fld_seg'=>"`{$this->fldname}` bigint(20) NOT NULL",
                'add_queries'=>[]
            ];
        }
    }


}
?>