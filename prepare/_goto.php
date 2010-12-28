<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
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

class _goto extends instruction {
    protected $label = null;

    function __construct($expression) {
        parent::__construct(array());
        
        $this->label = $expression[0];
    }

    function __toString() {
        $return = "GOTO ".$this->label;

        return $return;
    }

    function getLabel() {
        return $this->label;
    }

    function neutralise() {
        $this->label->detach();
    }

    function getRegex(){
        return array('goto_normal_regex',
                    );
    }

}

?>