<?php
include_once('Analyseur_Framework_TestCase.php');

class Try_Test extends Analyseur_Framework_TestCase
{
    /* 5 methodes */
    public function testTry1()  { $this->generic_test('try.1'); }
    public function testTry2()  { $this->generic_test('try.2'); }
    public function testTry3()  { $this->generic_test('try.3'); }
    public function testTry4()  { $this->generic_test('try.4'); }
    public function testTry5()  { $this->generic_test('try.5'); }

}

?>