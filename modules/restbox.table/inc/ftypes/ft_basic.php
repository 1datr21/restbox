<?php
namespace modules\restbox\table { 

    class fieldttype_base {
        
        VAR $fldname;
        VAR $PARAMS;
        VAR $_P_MODULE;
        VAR $isID = false;
        VAR $required = false;

        function __construct($_params=[],$_fldname,$_p_module=null)
        {
            $this->OnConstruct($_params);
            $this->fldname = $_fldname;
            $this->PARAMS = $_params;  
            
            def_options($this->def_params(),$this->PARAMS);
            

            $this->_P_MODULE =$_p_module;
            $this->AfterConstruct();
        }

        function def_params()
        {
            return ['require'=>false];
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

        function str_required()
        {
            if($this->PARAMS['require'])
                return " NOT NULL ";
            
            return " NULL ";
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

        function validate($_a_value)
        {
            if($this->required())
            {
                if(empty($_a_value))
                {
                    return "field {$this->fldname} could not be empty";
                }
            }
            if(isset($this->PARAMS['onvalidate']))
            {
                return $this->PARAMS['onvalidate']($_a_value,$this);
            }
            return null;
        }

        function OnCreateTable_std($_args)
        {
            
        }

        function OnChangeFld_std($_args)
        {
            return $this->OnCreateTable_std($_args);
        }

        function required()
        {
            return $this->PARAMS['require'];
        }

        function compare_fld($fld_map)
        {

        }

        function get_default()
        {
            if(isset($this->PARAMS['default']))
                return $this->PARAMS['default'];
            
            return null;
        }
    }
}