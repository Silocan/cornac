<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011 Alter Way Solutions (France)               |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Damien Seguy <damien.seguy@gmail.com>                        |
   +----------------------------------------------------------------------+
 */include_once('Analyseur_Framework_TestCase.php');

class Closure_Test extends Analyseur_Framework_TestCase
{
    /* 7 methodes */
    public function testClosure1()  { $this->generic_test('closure.1'); }
    public function testClosure2()  { $this->generic_test('closure.2'); }
    public function testClosure3()  { $this->generic_test('closure.3'); }
    public function testClosure4()  { $this->generic_test('closure.4'); }
    public function testClosure5()  { $this->generic_test('closure.5'); }
    public function testClosure6()  { $this->generic_test('closure.6'); }
    public function testClosure7()  { $this->generic_test('closure.7'); }

}

?>