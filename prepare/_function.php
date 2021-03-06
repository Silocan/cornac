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

class _function extends instruction {
    protected $name = '';
    protected $_abstract = null;
    protected $_static = null;
    protected $_visibility = null;
    protected $reference = null;
    protected $args = null;
    protected $block = null;
    

    function __construct($expression) {
        parent::__construct(array());
        
        while($expression[0]->checkToken(array(T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_ABSTRACT, T_FINAL))) {

            if ($expression[0]->checkToken(array(T_PUBLIC, T_PROTECTED, T_PRIVATE))) {
                $this->_visibility = $this->makeProcessedToken('_ppp_', $expression[0]);
                unset($expression[0]);
                $expression = array_values($expression);
                continue;
            }

            if ($expression[0]->checkToken(T_STATIC)) {
                $this->_static = $this->makeProcessedToken('_static_', $expression[0]);

                unset($expression[0]);
                $expression = array_values($expression);
                continue;
            }

            if ($expression[0]->checkToken(T_FINAL)) {
                $this->_abstract = $this->makeProcessedToken('_final_', $expression[0]);

                unset($expression[0]);
                $expression = array_values($expression);
                continue;
            } elseif ($expression[0]->checkToken(T_ABSTRACT)) {
                $this->_abstract = $this->makeProcessedToken('_abstract_', $expression[0]);

                unset($expression[0]);
                $expression = array_values($expression);
                continue;
            }
            
            // @note we should raise an error here... 
        }

        
        foreach($expression as $e) {
            if ($e->checkClass('arglist')) {
                $this->args = $e;
            } elseif ($e->checkClass('block')) {
                $this->block = $e;
            } elseif ($e->checkClass('literals')) {
                $this->name = $e;
            } elseif ($e->checkOperator('&')) {
                $this->reference = $this->makeProcessedToken('_reference_', $e);
            } elseif ($e->checkCode(';')) {
                $this->block = new block();
            } else {
            // @note this is an error. We should log this
                $this->stopOnError( $e." (".get_class($e).") Unknown\n");
            }
        }
    }
    
    function __toString() {
        return __CLASS__." function ".$this->name." (".$this->args.") {".$this->block."} ";
    }

    function getName() {
        return $this->name;
    }

    function getReference() {
        return $this->reference;
    }

    function getArgs() {
        return $this->args;
    }

    function getBlock() {
        return $this->block;
    }

    function getVisibility() {
        return $this->_visibility;
    }

    function getAbstract() {
        return $this->_abstract;
    }

    function getStatic() {
        return $this->_static;
    }

    function neutralise() {
    // @note always there
        $this->name->detach();
        $this->args->detach();

        if (!is_null($this->reference)) {   
            $this->reference->detach();
        }
        if (!is_null($this->_visibility)) {   
            $this->_visibility->detach();
        }
        if (!is_null($this->_static)) {   
            $this->_static->detach();
        }
        if (!is_null($this->_abstract)) {   
            $this->_abstract->detach();
        }
        
        if (!is_null($this->block)) {   
            $this->block->detach();
        }
    }

    function getRegex() {
        return array(
    'function_simple_regex',
    'function_reference_regex',
    'function_abstract_regex',
    'function_typehint_regex',
    'function_typehintreference_regex',
);
    }
}

?>