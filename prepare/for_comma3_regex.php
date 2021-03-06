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

class for_comma3_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_FOR);
    }
    
    function check($t) {
        if (!$t->hasNext(8)) { return false; }

        if ($t->checkNotToken(array(T_FOR))) { return false; } 
        if ($t->getNext()->checkNotCode(array('('))) { return false; } 

        $pos = 1;
        if ($t->getNext($pos)->checkCode(array(';'))) { 
            $pos++; 
        } elseif ($t->getNext($pos + 1)->checkNotCode(array(';'))) {  
            return false; 
        } else {
            $pos += 2;
        }

        if ($t->getNext($pos)->checkCode(array(';'))) { 
            $pos++; 
        } elseif ($t->getNext($pos + 1)->checkNotCode(array(';'))) {  
            return false; 
        } else {
            $pos += 2;
        }

        if ($t->getNext($pos)->checkClass(array('Token'))) { return false; } 
        if ($t->getNext($pos + 1)->checkNotCode(array(','))) { return false; } 
        
        $args = array(0);
        $remove = array(1);

        $pos_init = $pos;
        $pos = 2;
        $var = $t->getNext($pos_init + 2);
        
        while($var->checkNotClass('Token') &&
              $var->getNext()->checkCode(',')) {
            
            $args[] = $pos;
            
            $remove[] = $pos;
            $remove[] = $pos + 1;
            
            $var = $var->getNext(1);
            $pos += 2;
        }

        if ($var->checkNotClass('Token') &&
           $var->getNext()->checkCode(')')) {
            $args[] = $pos;
            
            $remove[] = $pos;

            $regex = new modele_regex('block',$args, $remove);
            Token::applyRegex($t->getNext($pos_init ), 'block', $regex);

            Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => block (position 3) (from ".get_class($t->getNext(3)).") (".__CLASS__.")");
            return false; 
        } 

        return false;
    }
}

?>