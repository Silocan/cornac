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

class _continue extends instruction {
    protected $levels = null;
    
    function __construct($expression = null) {
        parent::__construct(array());
        
        if (!isset($expression[1])) {
            $this->levels = new _continue_(1);
        } else {
            $this->levels = new _continue_($expression[1]->getCode());
        }
    }

    function __toString() {
        return __CLASS__." ".$this->code;
    }

    function getLevels() {
        return $this->levels;
    }

    function neutralise() {
    }

    function getRegex(){
        return array('continue_regex',
                     'continue_leveled_regex',
                    );
    }

}

?>