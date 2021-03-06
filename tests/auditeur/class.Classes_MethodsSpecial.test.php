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
 */
include_once('Auditeur_Framework_TestCase.php');

class Classes_MethodsSpecial_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->expected = array('a->__toString','a->__construct','a->a','a->__destruct','a->__clone','a->__set','a->__get','a->__call','a->__callStatic','a->__unset','a->__isset','a->__wakeup','a->__set_state','a->__sleep','a->__invoke','__autoload');
        $this->unexpected = array();
        
        parent::generic_test();
    }
}

?>