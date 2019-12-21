<?php 
namespace modules\restbox\table {

    class ft_file extends fieldttype_base
    {
        function OnCreate_std($_args)
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