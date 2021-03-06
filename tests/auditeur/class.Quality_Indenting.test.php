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

class Quality_Indenting_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->expected = array(
/*
'_class,_function,ifthen,_foreach,ifthen,_switch,_case,_while,_dowhile',
'_class,_function,ifthen,_foreach,ifthen,_switch,_case,_while',
'_class,_function,ifthen,_foreach,ifthen,_switch,_case',
'_class,_function,ifthen,_foreach,ifthen,_switch',
'_class,_function,ifthen,_foreach,ifthen',
'_class,_function,ifthen,_foreach',
*/
'_class,_function,ifthen',
'_class,_function',
'_class',
'_function',
 );
        $this->unexpected = array();
        
        parent::generic_test();
    }
}

?>