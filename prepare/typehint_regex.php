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

class typehint_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('(',',');
    }

    function check($t) {
    // @note Actually, we don't rely on ( or , but on the next token. 
        if (!$t->hasPrev(1)) {
        // @note too early, can't be a typehint
            return false; 
        } elseif ($t->getPrev()->checkToken(T_CATCH)) {
            return false;
            // @note this is a function
        } elseif ($t->getPrev(1)->checkToken(T_FUNCTION)) {
            // @note this is a function
        } elseif ($t->getPrev(1)->checkOperator('&') && 
                  $t->getPrev(2)->checkToken(T_FUNCTION)) {
            // @note this is a function
        } elseif ($t->getPrev()->checkClass('typehint')) {
            // @note there is a preceding typehint : this is OK
        } elseif ($t->hasNext(2) &&
                  $t->getNext( )->checkClass(array('Token','_nsname')) && 
                  $t->getNext(1)->checkClass(array('variable','affectation'))) {
            // @note this is a function
        } else { 
            return false; 
        }

        $t = $t->getNext();
        if ($t->checkNotToken(array(T_STRING, T_ARRAY)) &&
            $t->checkNotClass(array('_nsname'))) { return false; }

        if (!$t->hasNext() ) { return false; }

        if ($t->getNext()->checkNotClass(array('variable','affectation','reference'))) { return false; }
        if ($t->getNext(1)->checkCode(array('='))) { return false; }
        if ($t->getNext(1)->checkNotOperator(array(',',')'))) { return false; }

        $regex = new modele_regex('typehint',array(0, 1), array(1));
        Token::applyRegex($t, 'typehint', $regex);

        mon_log(get_class($t->getNext())." => typehint (".__CLASS__.")");
        return false;
    }
}
?>