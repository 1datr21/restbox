<?php
namespace modules\restbox\table { 

    class fieldttype_base {
        
        VAR $fldname;
        VAR $PARAMS;

        function __construct($_params=[],$_fldname)
        {
            $this->OnConstruct($_params);
            $this->fldname = $_fldname;
            $this->PARAMS = $_params;    
        }

        function OnConstruct(&$params_)
        {

        }

        function get_fields()
        {
            return [$this->fldname];
        }

        function OnCreateNewFld_std($_args)
        {
            return $this->OnCreateTable_std($_args);
        }

        function OnCreateTable_std($_args)
        {
            
        }

        function OnChangeFld_std($_args)
        {
            return $this->OnCreateTable_std($_args);
        }

        function compare_fld($fld_map)
        {

        }
    }
}