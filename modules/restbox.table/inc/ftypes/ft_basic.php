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

        function OnAlter_std($_args)
        {

        }

        function OnCreate_std($_args)
        {
            
        }
    }
}