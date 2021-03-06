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

class while_noblock_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_WHILE);
    }

    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->getNext()->checkNotClass('parenthesis')) { return false; }

        if ( $t->getNext(1)->checkOperator(';')) {
            
            // @note we wait for the block to be processed
            if ($t->getPrev()->checkOperator('}')) { return false; }
            if ($t->getPrev()->checkClass('Token') &&
                $t->getPrev()->checkNotCode('{') && 
                $t->getPrev()->checkNotToken(T_OPEN_TAG)) { return false; }
            // @note this is definitely not a while block
            if ($t->getPrev()->checkClass('block') &&              
                $t->getPrev(1)->checkToken(T_DO))  { return false; }
            $regex = new modele_regex('block',array(), array());
            Token::applyRegex($t->getNext(1), 'block', $regex);

            Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => block semi-colon (from ".get_class($t->getNext(1)).") (".__CLASS__.")");
            return false; 
        }

        if ($t->getNext(1)->checkClass(array('Token','block'))) { return false;}
        if ($t->getNext(2)->checkOperator(array('->','::','[','('))) { return false; }
        if ($t->getNext(2)->checkForAssignation()) { return false; }
        if ($t->getNext(2)->checkClass('arglist')) { return false; }

        $regex = new modele_regex('block',array(0), array());
        Token::applyRegex($t->getNext(1), 'block', $regex);

        Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => block (from ".get_class($t->getNext(1)).") (".__CLASS__.")");
        return false; 
    }
}
?>