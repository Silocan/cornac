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

class namespace_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_NAMESPACE);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }

        if ($t->getNext()->checkNotClass(array('_nsname','Token'))) { return false; }
        
        if ($t->getNext()->checkClass('_nsname')) {
            $this->args[] = 1;
            $this->remove[] = 1;

            Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => ".__CLASS__);
            return true; 
        } elseif ($t->getNext()->checkClass('Token')) {
            if ($t->getNext()->checkCode(array(',','=>',';',')'))) { return false; }
            if ($t->getNext()->checkToken(array(T_CLOSE_TAG))) { return false; }
            // @note allow \ to appear after. 
            if ($t->hasNext(2) && $t->getNext(1)->checkOperator('\\')) { return false; }
            
            $regex = new modele_regex('_nsname',array(0), array());
            Token::applyRegex($t->getNext(), '_nsname', $regex);

            Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => _nsname");

            return false;
        } // @empty_elseif
    }
}
?>