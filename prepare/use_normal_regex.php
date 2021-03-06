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

class use_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_USE);
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        $var = $t;
        while($var->checkOperator(',') || $var->checkToken(T_USE)) {
            if ($var->getNext()->checkClass(array('Token')) &&
                $var->getNext(1)->checkToken(T_AS) &&
                $var->getNext(2)->checkClass('Token') &&
                $var->getNext(3)->checkOperator(array(',',';'))) { 

                $regex = new modele_regex('_nsname',array(0), array());
                Token::applyRegex($var->getNext(), '_nsname', $regex);
    
                Cornac_Log::getInstance('tokenizer')->log(get_class($t)." =>2 ".__CLASS__);
                
                $var = $var->getNext(1);
                continue;
            }
            if ($var->getNext()->checkClass('Token') &&
                $var->getNext(1)->checkOperator(array(',',';'))) { 

                if ($var->getNext()->checkOperator(array('(','*','+','-','/','^',',','=>','}',';',')'))) { return false; }
                // @note allow \ to appear after. 
                if ($var->getNext(1)->checkOperator('\\')) { return false; }
            
                $regex = new modele_regex('_nsname',array(0), array());
                Token::applyRegex($var->getNext(), '_nsname', $regex);
    
                Cornac_Log::getInstance('tokenizer')->log(get_class($t)." =>1 ".__CLASS__);
                
                $var = $var->getNext(1);
                continue;
            }

            // @note case of the as. Skip 2
            if ($var->getNext(1)->checkToken(T_AS)) { 
                $var = $var->getNext(3);
            } elseif ($var->getNext()->checkClass('Token')) { 
                return false; 
            } else {
                $var = $var->getNext(1);
            }
        }
        
        if ($var->checkNotOperator(';')) {
            return false;
        }
        
        $var = $t;
        while($var->checkOperator(',') || $var->checkToken(T_USE)) {
                // @note registering a new global each comma
                    $args = array(1);
                    $remove = array(1);

                    $repl = $var;
                    if ($var->getNext(1)->checkToken(T_AS)) {
                        $args[] = 3;
                        $remove[] = 2;
                        $remove[]=  3;
                        $var = $var->getNext(3);
                    } else {
                        $var = $var->getNext(1);
                    }


                    $regex = new modele_regex('_use',$args, $remove);
                    Token::applyRegex($repl, '_use', $regex);

                    Cornac_Log::getInstance('tokenizer')->log(get_class($var)." => _use  (".__CLASS__.")");
                    continue;
        }

        return false;
    }
}
?>