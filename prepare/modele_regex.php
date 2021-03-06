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

// @note this is a skeleton for patterns. 
class modele_regex extends analyseur_regex {

    function __construct($class, $args, $remove) {
        parent::__construct(array());
        
        $this->class = $class;
        $this->args = $args;
        $this->remove = $remove;
    }

    function getTokens() {
        return array();
    }
    
    function check($t) {
        Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => Modele ({$this->class}) ");
        return true;
    }
}
?>