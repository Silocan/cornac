<?php

class _var extends instruction {
    protected $_static = null;
    protected $_visibility = null;
    protected $variable = array();
    protected $init = array();

    function __construct($expression) {
        parent::__construct(array());

        while ($expression[0]->checkToken(array(T_VAR, T_PRIVATE, T_PROTECTED, T_PUBLIC, T_STATIC))) {
            if ($expression[0]->checkToken(array(T_VAR, T_PRIVATE, T_PROTECTED, T_PUBLIC))) {
                $this->_visibility = $this->make_token_traite($expression[0]);
            } elseif ($expression[0]->checkToken(array(T_STATIC))) {
                $this->_static = $this->make_token_traite($expression[0]);
            } else {
                $this->stop_on_error("Unknown class attribute : ".count($expression)." in ".__METHOD__);
            }

            unset($expression[0]);
            $expression = array_values($expression);
        }
        
        foreach($expression as $id => $e) {
            if ($e->checkClass('variable')) {
                $this->variable[] = $e;
                $this->init[] = null;
            } elseif ($e->checkClass('affectation')) {
                $this->variable[] = $e->getDroite();
                $this->init[] = $e->getGauche();
            } elseif ($e->checkClass('arginit')) {
                $this->variable[] = $e->getVariable();
                $this->init[] = $e->getValeur();        
            } else {
                $this->stop_on_error(" Unexpected class for ".__CLASS__." : ".get_class($e)." $e $id in ".__METHOD__);
            }
        }
    }
    
    function __toString() {
         $return = __CLASS__." ".$this->getVisibility();
         
         $r = array();
         foreach($this->variable as $id => $variable) {
            $r[] = $variable;
         }
         $return .= join(', ', $r);
         return $return;
    }

    function getVariable() {
        return $this->variable;
    }

    function getInit() {
        return $this->init;
    }

    function getVisibility() {
        return $this->_visibility;
    }

    function getStatic() {
        return $this->_static;
    }

    function neutralise() {
        if (count($this->variable)) {
            foreach($this->variable as $id => &$e) {
                $e->detach();
                if (!is_null($this->init[$id])) {
                    $this->init[$id]->detach();
                }
            }
        }
        if (!is_null($this->_static)) {
            $this->_static->detach();
        }
        if (!is_null($this->_visibility)) {
            $this->_visibility->detach();
        }
    }

    function getRegex() {
        return array(
                      'var_simple_regex',
                    );
    }
}

?>