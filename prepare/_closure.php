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

class _closure extends instruction {
    protected $block = null;
    protected $args = null;
    
    function __construct($expression) {
        parent::__construct(array());
        
        if ($expression[0]->checkClass('arglist')) {
            $this->args = $expression[0];
            array_shift($expression);
            $expression = array_values($expression);
        } else {
            $this->args = new arglist();
        }

        $this->block = $expression[0];
    }

    function __toString() {
        return "function() ".$this->block;
    }

    function getBlock() {
        return $this->block;
    }

    function getArgs() {
        return $this->args;
    }

    function neutralise() {
        $this->args->detach();
        $this->block->detach();
    }

    function getRegex(){
        return array('closure_normal_regex',
                    );
    }

}

?>