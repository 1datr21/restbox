<?php
namespace modules\restbox {

    class obj_description {
        VAR $_info;

        function __construct($__info)
        {
            $this->_info = $__info;
        }

        function getInfo()
        {
            return $this->_info;
        }
    }

}