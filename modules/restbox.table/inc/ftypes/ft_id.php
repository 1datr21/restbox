<?php 
namespace modules\restbox\table {
    class ft_id extends fieldttype_base
    {
       
        function __construct($params=[],$_fldname='id')
        {
            def_options([
                'fld_type'=>'bigint',
                'size'=>20,
                'AUTO_INCREMENT'=>true
            ],$params);
            parent::__construct($params,$_fldname);
        }

        function OnCreate_std($_args)
        {
            return [
                'fld_seg'=>"`{$this->fldname}` bigint(20) NOT NULL",
                'add_queries'=>[
                    "ALTER TABLE `@+[table]` ADD PRIMARY KEY (`{$this->fldname}`);",
                    "ALTER TABLE `@+[table]` MODIFY `{$this->fldname}` bigint(20) NOT NULL AUTO_INCREMENT",
                  'COMMIT;'
                ]
            ];
        }
    }


}
?>