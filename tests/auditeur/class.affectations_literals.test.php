<?php
include_once('Auditeur_Framework_TestCase.php');

class affectations_literals_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = substr(basename(__FILE__), 6, -9);
        $this->attendus = array('$a = 1',
'$b = 2 + 3',
'$e = array()',);
        $this->inattendus = array('$c = $d',);
        
        parent::generic_test();
    }
}

?>