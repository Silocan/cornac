<?php
include_once('Analyseur_Framework_TestCase.php');

class Static_Test extends Analyseur_Framework_TestCase
{
    /* 3 methodes */
    public function testStatic1()  { $this->generic_test('static.1'); }
    public function testStatic2()  { $this->generic_test('static.2'); }
    public function testStatic3()  { $this->generic_test('static.3'); }

}

?>