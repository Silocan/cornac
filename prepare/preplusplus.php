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

class preplusplus extends instruction {
    protected $variable = null;
    protected $operator = null;
    
    function __construct($expression) {
        parent::__construct(array());
        
        $this->operator = $this->makeProcessedToken('_preplusplus_', $expression[0]);
        $this->variable  = $expression[1];
    }

    function __toString() {
        return __CLASS__." ".$this->variable.$this->operator;
    }

    function getVariable() {
        return $this->variable;
    }

    function getOperator() {
        return $this->operator;
    }

    function neutralise() {
        $this->variable->detach();
        $this->operator->detach();
    }

    function getRegex(){
        return array('preplusplus_regex'
                    );
    }

}

?>