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

class clone_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_CLONE);
    }
 
    
    function check($t) {
        if (!$t->hasNext()) { return false; }

        if ($t->getNext()->checkNotClass(array('variable','_array',
                                            'property','property_static',
                                            'method','method_static',
                                            'functioncall', '_new'))) { return false; }
        if (!$t->getNext(1)->checkEndInstruction()) { return false; }

        $this->args = array(1);
        $this->remove = array(1);

        Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>