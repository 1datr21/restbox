<?php
namespace modules\restbox\db\mysql {
    use Core;
    use Exception;
    use modules\restbox\db\RBDBConnection as RBDBConnection;
    use modules\restbox\RBModule as RBModule;
    
    class MySQLiConnection extends RBDBConnection {


        function get_error()
		{
            return mysqli_connect_error();
		}
    }

}