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

class ifthenelseif_simple_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_IF,T_ELSEIF);
    }
    
    function check($t) {
        if (!$t->hasNext(1) ) { return false; }
        if ($t->getNext()->checkNotClass('parenthesis')) { return false; }

        if ($t->getNext(1)->checkForBlock(true)) {
            if ($t->hasNext(2) && $t->getNext(2)->checkForAssignation()) {
                return false;
            }

            if ($t->hasNext(2) && $t->getNext(2)->checkCode(array('->','[','::'))) {
                return false;
            }
            
            $remove = array();
            if ($t->hasNext(2) && 
                $t->getNext(2)->checkCode(';')) {
                $remove = array(1);
            }
            $regex = new modele_regex('block',array(0),$remove);
            Token::applyRegex($t->getNext(1), 'block', $regex);

            Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => block 1 (".__CLASS__.")");
            return false; 
        } 

        if ($t->getNext(1)->checkNotClass('Token') &&
            $t->getNext(2)->checkCode(';')) {

            $regex = new modele_regex('block',array(0), array(1));
            Token::applyRegex($t->getNext(1), 'block', $regex);

            Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => block 2 (".__CLASS__.")");
            return false; 
        } 
        
        return false;
    }
}
?>