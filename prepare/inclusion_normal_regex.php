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

class inclusion_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_REQUIRE, T_INCLUDE, T_INCLUDE_ONCE, T_REQUIRE_ONCE);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }

        if ($t->checkToken(array(T_REQUIRE, T_INCLUDE, T_INCLUDE_ONCE, T_REQUIRE_ONCE)) &&
            $t->getNext()->checkCode('(') &&
            $t->getNext(1)->checkNotClass('Token') &&
            $t->getNext(2)->checkCode(')')
            
            ) {

            $this->args = array(2);
            $this->remove = array(1,2,3);

            Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => inclusion (".__CLASS__.")");
            return true; 
        } 
        return false;
    }
}
?>