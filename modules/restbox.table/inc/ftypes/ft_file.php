<?php 
namespace modules\restbox\table {

    class ft_file extends fieldttype_base
    {
        function OnCreateTable_std($_args)
        {
            switch($this->PARAMS['mode']){
                case 'blob' : $seg = "`{$this->fldname}` longblob  {$this->str_required()} , `{$this->fldname}_mime` text  {$this->str_required()} ";break;
                case 'url' : $seg = "`{$this->fldname}` text  {$this->str_required()} ";break;
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
                    $q_add[]= "ALTER TABLE `@+[table]` ADD COLUMN `{$this->fldname}_mime` text  {$this->str_required()} AFTER `{$this->fldname}`";
                    $seg = "`{$this->fldname}` longblob  {$this->str_required()}  ";
                    break;
                case 'url' : $seg = "`{$this->fldname}` text  {$this->str_required()} ";break;
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