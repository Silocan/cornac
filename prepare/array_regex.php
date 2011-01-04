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

class array_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('[');
    }
    
    function check($t) {
        if (!$t->hasPrev(1) ) { return false; }
        if (!$t->hasNext(1) ) { return false; }

        if ($t->getPrev()->checkNotClass(array('variable','_array','property','opappend'))) { return false; }

        if ($t->getNext()->checkClass('Token')) { return false; }
        if ($t->getNext(1)->checkNotCode(']'))  { return false; }

        $this->args   = array(-1, 1);
        $this->remove = array(-1,0,1,2);

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>