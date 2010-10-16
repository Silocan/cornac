<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
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

class catch_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_CATCH );
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->getNext()->checkOperator('(') &&
            $t->getNext(3)->checkOperator(')') &&
            $t->getNext(4)->checkClass('block') 
            ) {
            
            $this->args = array(2, 3, 5);
            $this->remove = array(1,2,3,4,5);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 

        return false;
    }
}
?>