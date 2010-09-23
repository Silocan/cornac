<?php 
include_once('Auditeur_Framework_TestCase.php');

class session_functions_Test extends Auditeur_Framework_TestCase
{
    public function testsession_functions()  {
        $this->expected = array( 
'session_name',
'session_module_name',
'session_save_path',
'session_id',
'session_regenerate_id',
'session_decode',
'session_register',
'session_unregister',
'session_is_registered',
'session_encode',
'session_start',
'session_destroy',
'session_unset',
'session_set_save_handler',
'session_cache_limiter',
'session_cache_expire',
'session_set_cookie_params',
'session_get_cookie_params',
'session_write_close',
'session_commit',
        );
        $this->unexpeted = array(/*'',*/);

        parent::generic_test();
    }
}
?>
