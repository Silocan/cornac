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

class sequence_class_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(Token::ANY_TOKEN);
    } 

    function check($t) {
        if (!$t->hasNext() ) { return false; }

        if (!$t->checkForBlock(true) && $t->checkNotClass(array('codephp'))) { return false; } 
        if (!$t->getNext()->checkForBlock(true) && 
            !$t->getNext()->checkForVariable() &&
            $t->getNext()->checkNotClass(array('parentheses')) ) { return false; } 
        if ( (!$t->hasNext(1) || 
               ($t->getNext(1)->checkNotCode(array('or','and','xor','->','[','::',')','.','^','&','|','||','&&','++','--','+','-','/','*','%')) &&
                !$t->getNext(1)->checkForAssignation()) &&
                $t->getNext(1)->checkNotClass('arglist'))
               ) { 

            if ($t->hasNext(1) && $t->getNext(1)->checkCode(array('=','->',',','('))) { return false; }
            if ($t->hasPrev() && ($t->getPrev()->checkCode(array(')',':','->','.','?','"')) ||
                                  $t->getPrev()->checkClass(array('parentheses','arglist')) ||
                                  $t->getPrev()->checkForAssignation() || 
                                  $t->getPrev()->checkToken(array(T_ELSE, T_ABSTRACT))) ) { return false; }

            $var = $t->getNext(1); 
            $this->args   = array( 0, 1 );
            $this->remove = array( 1 );
                        
            mon_log(get_class($t)." repère une sequence ( ".get_class($t).", ".get_class($t->getNext())." )  (".__CLASS__.")");
            return true; 
        } 
        
        return false;
    }

}
?>