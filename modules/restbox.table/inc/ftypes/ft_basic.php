<?php
namespace modules\restbox\table { 

    class fieldttype_base {
        
        VAR $fldname;
        VAR $PARAMS;
        VAR $_P_MODULE;

        function __construct($_params=[],$_fldname,$_p_module=null)
        {
            $this->OnConstruct($_params);
            $this->fldname = $_fldname;
            $this->PARAMS = $_params;    
            $this->_P_MODULE =$_p_module;
            $this->AfterConstruct();
        }

        function OnConstruct(&$params_)
        {

        }

        function AfterConstruct()
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