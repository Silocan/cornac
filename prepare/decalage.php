<?php

class decalage extends instruction {
    protected $gauche = null;
    protected $operateur = null;
    protected $droite = null;
    
    function __construct($expression = null) {
        parent::__construct(array());

        $this->gauche = $expression[0];
        $this->operateur = $this->make_token_traite($expression[1]);
        $this->droite = $expression[2];
    }

    function __toString() {
        return __CLASS__." ".$this->gauche." "." ".$this->operateur." "." ".$this->droite." ";
    }

    function getDroite() {
        return $this->droite;
    }

    function getOperateur() {
        return $this->operateur;
    }

    function getGauche() {
        return $this->gauche;
    }

    function neutralise() {
        $this->gauche->detach();
        $this->operateur->detach();
        $this->droite->detach();
    }

    function getRegex(){
        return array('decalage_regex',
                    );
    }

}

?>