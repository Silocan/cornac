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

class method_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_OBJECT_OPERATOR);
    }
    
    function check($t) {
        if (!$t->hasNext() ) { return false; }
        if (!$t->hasPrev() ) { return false; }

        if ($t->hasPrev(1) && $t->getPrev(1)->checkOperator(array('->','::'))) { return false; }

        if ($t->getPrev()->checkNotClass(array('variable',
                                               'property',
                                               '_array',
                                               'method',
                                               'functioncall',
                                               'property_static',
                                               'method_static'))) { return false;}
        if ($t->checkNotOperator('->')) { return false; }
        if ($t->getNext()->checkNotClass('functioncall')) { return false; }

        $this->args   = array(-1, 1);
        $this->remove = array(-1, 0, 1);

        Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>