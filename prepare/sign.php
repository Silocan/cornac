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

class sign extends instruction {
    protected $sign = null;
    protected $expression = null;
    
    function __construct($expression = null) {
        parent::__construct(array());

        $this->sign = $this->makeProcessedToken('_sign_', $expression[0]);
        $this->expression = $expression[1];
    }

    function __toString() {
        return __CLASS__." ".$this->sign.$this->expression;
    }

    function getExpression() {
        return $this->expression;
    }

    function getSign() {
        return $this->sign;
    }

    function neutralise() {
        $this->sign      ->detach();
        $this->expression->detach();
    }

    function getRegex(){
        return array('sign_regex',
                     'sign_suite_regex',
                    );
    }

}

?>