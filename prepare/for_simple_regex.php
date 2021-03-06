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

class for_simple_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_FOR);
    }
    
    function check($t) {
        if (!$t->hasNext(4)) { return false; }

        if ($t->getNext()->checkNotOperator(array('('))) { return false; } 

        $args = array();
        $remove = array(1);
        $pos = 1;

        if ($t->getNext($pos)->checkOperator(';')) {
            $args[] = $pos + 1;

            $remove[] = $pos + 1;
            
            $pos += 1;
        } elseif ($t->getNext($pos)->checkClass(array('Token','sequence')) ) {
            return false; 
        } elseif ($t->getNext($pos)->checkClass('block') ) {
            $args[] = $pos + 1  ;

            $remove[] = $pos  + 1;
            
            $pos += 1;
            if ($t->getNext($pos)->checkOperator(';')) {
                $remove[] = $pos  + 1;
                $pos += 1;
            }
        } elseif ($t->getNext($pos)->checkNotClass(array('Token','sequence')) &&
            $t->getNext($pos + 1)->checkCode(';'))
        {
            $args[] = $pos + 1  ;

            $remove[] = $pos  + 1;
            $remove[] = $pos + 1  + 1;
            
            $pos += 2;
        } else { // @doc Not a Token followed by ;, we ignore
            return false;
        }

        if ($t->getNext($pos)->checkCode(';')) {
            $args[] = $pos + 1;

            $remove[] = $pos + 1;
            
            $pos += 1;
        } elseif ($t->getNext($pos)->checkClass(array('Token')) ) {
            return false; 
        } elseif ($t->getNext($pos)->checkClass(array('block','sequence')) ) {
            $args[] = $pos + 1  ;
            $remove[] = $pos  + 1;
            $pos += 1;
            
            if ($t->getNext($pos)->checkOperator(';')) {
                $remove[] = $pos  + 1;
                $pos += 1;
            }
        } elseif ($t->getNext($pos)->checkNotClass(array('Token','sequence')) &&
            $t->getNext($pos + 1)->checkCode(';')
        ) {
            $args[] = $pos + 1  ;

            $remove[] = $pos  + 1;
            $remove[] = $pos + 1  + 1;
            
            $pos += 2;
        } else { // @doc Not a Token followed by ;, we ignore
            return false;
        }

        if ($t->getNext($pos)->checkCode(')')) {
            $args[] = $pos + 1;

            $remove[] = $pos + 1;
            
            $pos += 1;
        } elseif ($t->getNext($pos)->checkClass(array('Token','sequence')) ) {
            return false; 
        } elseif ($t->getNext($pos)->checkNotClass(array('Token','sequence')) &&
                  !is_null($t->getNext($pos + 1)) && 
                  $t->getNext($pos + 1)->checkCode(')')
        ) {
            $args[] = $pos + 1  ;

            $remove[] = $pos  + 1;
            $remove[] = $pos + 1  + 1;
            
            $pos += 2;
        } else { // @doc Not a Token followed by ;, we ignore
            return false;
        } 

        if ($t->getNext($pos)->checkCode(';')) {
            $regex = new modele_regex('block',array(), array());
            Token::applyRegex($t->getNext($pos), 'block', $regex);

            Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => block (position $pos) (from ; ) (".__CLASS__.")");            
            // @note no return, we carry on
        }

        if ($t->getNext($pos)->checkForBlock(true) && 
            (is_null($t->getNext($pos + 1)) ||
             $t->getNext($pos + 1)->checkNotOperator(array('(','->','::','=','[')))) {
              $args[] = $pos + 1;
              $remove[] = $pos + 1;

              $this->args = $args;
              $this->remove = $remove;
              
              Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => ".__CLASS__);
              return true;
        } else {
            return false;
        }
    }
}

?>