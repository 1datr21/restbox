<?php 
namespace modules\restbox\table {

    class ft_file extends fieldttype_base
    {
        function OnCreateTable_std($_args)
        {
            switch($this->PARAMS['mode']){
                case 'blob' : $seg = "`{$this->fldname}` longblob NOT NULL , `{$this->fldname}_mime` text NOT NULL ";break;
                case 'url' : $seg = "`{$this->fldname}` text NOT NULL ";break;
            }
            return [
                'fld_seg'=>$seg,
                'add_queries'=>[]
            ];
        }

        function OnCreateNewFld_std($_args)
        {
            $q_add = [];
            switch($this->PARAMS['mode']){
                case 'blob' :
                    $q_add[]= "ALTER TABLE `@+[table]` ADD COLUMN `{$this->fldname}_mime` text NOT NULL AFTER `{$this->fldname}`";
                    $seg = "`{$this->fldname}` longblob NOT NULL  ";
                    break;
                case 'url' : $seg = "`{$this->fldname}` text NOT NULL ";break;
            }
            return [
                'fld_seg'=>$seg,
                'add_queries'=>$q_add
            ];
        }

        function OnConstruct(&$params_)
        {
            def_options(['mode'=>'url'],$params_);
        }

        function get_fields()
        {
            if( $this->PARAMS['mode']=='blob')
            {
                return [$this->fldname, $this->fldname."_mime"];
            }
            return [$this->fldname];
        }
    }

}
?>