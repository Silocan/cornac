<?php

class _continue extends instruction {
    protected $expression = null;
    
    function __construct($expression = null) {
        parent::__construct(array());
        
        if (!isset($expression[1])) {
            $this->niveaux = new token_traite(1);
        } else {
            $this->niveaux = new token_traite($expression[1]->getCode());
        }
    }

    function __toString() {
        return __CLASS__." ".$this->code;
    }

    function getNiveaux() {
        return $this->niveaux;
    }


    function neutralise() {
//        $this->expression->detach();
    }

    function getRegex(){
        return array('continue_regex',
                     'continue_leveled_regex',
                    );
    }

}

?>