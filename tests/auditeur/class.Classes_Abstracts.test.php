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
 */include_once('Auditeur_Framework_TestCase.php');

class Classes_Abstracts_Test extends Auditeur_Framework_TestCase
{
    public function testabstracts()  {
        $this->expected = array( 
'abstract_class::public_abstract_static_method',
'abstract_class::abstract_public_static_method',
'abstract_class::abstract_protected_static_method',
'abstract_class::protected_abstract_static_method',
'abstract_class::protected_static_abstract_method',
'abstract_class::public_static_abstract_method',
'abstract_class::abstract_protected_method',
'abstract_class::protected_abstract_method',
'abstract_class::abstract_public_method',
'abstract_class::public_abstract_method',
'abstract_class',
        );
        $this->unexpected = array('real_method',);

        parent::generic_test();
//        parent::generic_counted_test();
    }
}
?>