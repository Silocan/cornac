<?php
include_once('Auditeur_Framework_TestCase.php');

class affectations_direct_gpc_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = substr(basename(__FILE__), 6, -9);
        $this->attendus = array('$_COOKIE',
'$_GET',
'$_POST',
'$_REQUEST',
'$HTTP_GET_VARS',
'$HTTP_POST_VARS');
        $this->inattendus = array('$i','$j','$k','$a','$b','$c','$d','$e','$g','$h');
        
        parent::generic_test();
    }
}

?>
