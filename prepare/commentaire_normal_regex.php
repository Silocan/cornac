<?php

class commentaire_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }
    
    function check($t) {
        if ($t->checkToken(array(T_COMMENT, T_DOC_COMMENT))) {
            $this->args = array();
            $this->remove = array();
            
            $return = $t->getPrev();
            $return->removeNext();
            
            return $return; 
        } else {
            return $t;
        }

    }
}

?>