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

class method_static extends instruction {
    protected $class = null;
    protected $method = null;
    
    function __construct($expression) {
        parent::__construct(array());
        
        if (is_array($expression)) {
            $this->class = $this->make_token_traite($expression[0]);
            $this->method = $expression[1];
        } else {
            $this->stopOnError('Bad call of '.__METHOD__." ".join(', ',func_get_args()));
        }
    }

    function getClass() {  
        return $this->class;
    }

    function getMethod() {
        return $this->method;
    }

    function getCode() {
        return '';
    }

    function neutralise() {
        $this->class->detach();
        $this->method->detach();
    }

    function __toString() {
        return __CLASS__." ".$this->class."::".$this->method;
    }

    function getRegex(){
        return array('method_static_regex',
                     );
    }

}

?>