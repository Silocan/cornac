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

class literals_heredoc_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_START_HEREDOC);
    }
    
    function check($t) {
            
        if ($t->getNext()->checkNotToken(T_ENCAPSED_AND_WHITESPACE)) { return false; }
        if ($t->getNext(1)->checkNotToken(T_END_HEREDOC)) { return false; }

        $this->args = array(0, 1);
        $this->remove = array(1,2);

        Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => ".__CLASS__);
        return true;
    }
}

?>