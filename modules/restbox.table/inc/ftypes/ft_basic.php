<?php
namespace modules\restbox\table { 

    class fieldttype_base {
        
        VAR $fldname;
        VAR $PARAMS;
        VAR $_P_MODULE;
        VAR $isID = false;

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

        function BeforeInsert(&$params)
        {

        }

        function AfterInsert(&$params)
        {

        }

        function BeforeUpdate(&$params)
        {

        }

        function AfterUpdate(&$params)
        {

        }


        function AfterConstruct()
        {

        }

        function isID()
        {
            return $this->isID;
        }

        function get_fields()
        {
            return [$this->fldname];
        }

        function OnCreateNewFld_std($_args)
        {
            return $this->OnCreateTable_std($_args);
        }

        public function getDefault()
        {
            if(isset($this->PARAMS['default']))
            {
                return $this->PARAMS['default'];
            }
            return null;
        }

        public function ValueList()
        {
            return null;
        }

        public function validate($_a_value)
        {
            return null;
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