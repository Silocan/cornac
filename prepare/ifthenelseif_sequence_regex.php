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

class ifthenelseif_sequence_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_IF,T_ELSEIF);
    }
    
    function check($t) {
        if (!$t->hasNext(2) ) { return false; }

        if ($t->checkToken(array(T_ELSEIF, T_IF)) &&
            $t->getNext()->checkClass('parenthesis') &&
            $t->getNext(1)->checkClass('sequence')
            ) {

            $regex = new modele_regex('block',array(0), array());
            Token::applyRegex($t->getNext(1), 'block', $regex);

            Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => block (".__CLASS__.")");
            return false; 
        } 
        return false;
    }
}
?>