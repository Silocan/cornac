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

class functioncall_echowithoutparenthesis_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_ECHO);
    }

    function check($t) {
        if (!$t->hasNext(1) ) { return false; }
        
        if ($t->getNext()->checkOperator('(')) { return false; }

        $var = $t->getNext(); 
        $args   = array();
        $remove = array();
    
        $pos = 0;
    
        while ($var->checkNotClass('Token') && 
               $var->checkNotOperator(array(';',',')) &&
               $var->getNext()->checkOperator(',')) {

            $args[]    = $pos;

            $remove[]  = $pos;
            $remove[]  = $pos + 1;
        
            $pos += 2;
            if (is_null($var)) { return false; }
            $var = $var->getNext(1);
            if (is_null($var)) { return false; }
        }
        
        if ($var->checkNotClass(array('Token','arglist')) && 
            $var->getNext()->checkEndInstruction() &&
            $var->getNext()->checkNotClass('parenthesis')
            ) {
            
            if ($var->getNext()->checkOperator(array('?'))) { return false; }
            $args[]    = $pos;
            $remove[]  = $pos;

            $regex = new modele_regex('arglist',$args, $remove);
            Token::applyRegex($t->getNext(), 'arglist', $regex);

            Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => arglist (".__CLASS__.")");
            return false; 
        } else {
            return false;
        }
    }
}
?>