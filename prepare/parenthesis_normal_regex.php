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

class parenthesis_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('(');
    }
    
    function check($t) {
        if (!$t->hasPrev() ) { return false; }
        if (!$t->hasNext(1)) { return false; }

        if ($t->checkNotClass('Token')) { return false; }
        if ($t->getPrev()->checkClass('variable')) { return false; }
        if ($t->getPrev()->checkToken(array(T_CONTINUE, T_USE, T_FUNCTION))) { return false; }
        if ($t->getPrev()->checkOperator('}')) { return false; }
        if ($t->getNext()->checkClass('Token')) { return false; }
        if ( $t->getNext(1)->checkNotCode(')')) { return false; }

        if ($t->getPrev()->checkFunction() ) { 
            if ($t->getPrev()->checkCode('echo')) {
                // case of $object->echo(); 
                if ($t->getPrev(1)->checkOperator('->')) { return false; }
                // @note this is possible, we shall go on
            } else {
                return false; 
            }
        } elseif ($t->getPrev()->checkClass(array('property','_array','property_static')) ||
                  $t->getPrev()->checkOperator(']')) {
            // @note this may be a $obj->$array[1]() call
            return false; 
        } // @empty_elseif
        
        $this->args = array(1);
        $this->remove = array(1, 2);

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>