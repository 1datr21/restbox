<?php
namespace modules\restbox {

    class obj_description {
        VAR $_info;

        function __construct($__info)
        {
            $this->_info = $__info;
        }

        function set_p_module($_module)
        {
            $this->P_MODULE = $_module;
        }

        function getInfo()
        {
            return $this->_info;
        }
    }

}