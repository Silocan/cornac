<?php

class template_left extends template {
    protected $root = null;
    protected $stats = 0;
    
    function __construct($root) {
        parent::__construct();
        
        $this->root = $root;
    }
    
    function affiche($noeud = null, $niveau = 0) {
        if (is_null($noeud)) {
            $noeud = $this->root;
        }
        
        if (!is_object($noeud)) {
            print "Attention, on tente un affichage d'une variable qui n'est pas un objet dans template.stats\n\n";
            debug_print_backtrace();
            die();
        }
        $class = get_class($noeud);
        $method = "affiche_$class";

        if (method_exists($this, $method)) {
            $this->$method($noeud, $niveau + 1);
        } else {
            $this->stats['missing'][$method] = 1;
        }
        if (!is_null($noeud->getNext())){
            $this->affiche($noeud->getNext(), $niveau);
        } else {
            if ($niveau == 0) {
                if (count($this->stats['missing']) == 0) { 
                    unset($this->stats['missing']); 
                }
            }
        }
    }
    
    function affiche_arglist($noeud, $niveau) {
        
        $elements = $noeud->getList();
        if (is_array($elements)) {
            foreach($elements as $id => $e) {
                $this->affiche($e, $niveau + 1);
            }
        }
    }

    function affiche_affectation($noeud, $niveau) {
        
        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);
    }

    function affiche_block($noeud, $niveau) {
        
        $elements = $noeud->getList();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche__break($noeud, $niveau) {
        
    }

    function affiche_comparaison($noeud, $niveau) {
        
        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);
    }
    
    function affiche_cdtternaire($noeud, $niveau) {
        
        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getVraie(), $niveau + 1);
        $this->affiche($noeud->getFaux(), $niveau + 1);
    }

    function affiche_codephp($noeud, $niveau) {
        
        $this->affiche($noeud->getphp_code(), $niveau + 1);
    }

    function affiche_concatenation($noeud, $niveau) {
        
        $elements = $noeud->getList();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche_constante($noeud, $niveau) {
        
    }

    function affiche__for($noeud, $niveau) {
        
        $this->affiche($noeud->getInit(), $niveau + 1);
        $this->affiche($noeud->getFin(), $niveau + 1);
        $this->affiche($noeud->getIncrement(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche__foreach($noeud, $niveau) {
        

        $gets = array('getTableau','getKey','getValue','getBlock');

        foreach($gets as $get) {
            $list = $noeud->$get();
            if (!is_null($list)) {
                $this->affiche($list, $niveau + 1);
            }
        }
    }

    function getKey() {
        return $this->key;
    }

    function getValue() {
        return $this->value;
    }

    function getBlock() {    }

    function affiche_functioncall($noeud, $niveau) {
        

        $args = $noeud->getArgs();
        $this->affiche($args, $niveau + 1);
    }

    function affiche_ifthen($noeud, $niveau) {
        
        $conditions = $noeud->getCondition();
        $thens = $noeud->getThen();
        foreach($conditions as $id => $condition) {
            $this->affiche($condition, $niveau + 1);
            $this->affiche($thens[$id], $niveau + 1);
        }
        if (!is_null($noeud->getElse())){
            $this->affiche($noeud->getElse(), $niveau + 1);
        }
    }

    function affiche_inclusion($noeud, $niveau) {
        
        $inclusion = $noeud->getInclusion();
        $this->affiche($inclusion, $niveau + 1);
    }

    function affiche_logique($noeud, $niveau) {
        
        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);
    }

    function affiche_literals($noeud, $niveau) {
        
    }

    function affiche_method($noeud, $niveau) {
        
        $this->affiche($noeud->getObject(), $niveau + 1);
        $this->affiche($noeud->getMethod(), $niveau + 1);
    }

    function affiche__new($noeud, $niveau) {
         print str_repeat('  ', $niveau).' new '.$noeud->getClasse()." ".$noeud->getArgs()." \n";
    }

    function affiche_noscream($noeud, $niveau) {
        
        $this->affiche($noeud->getExpression(), $niveau + 1);
    }

    function affiche_opappend($noeud, $niveau) {
        
        $this->affiche($noeud->getVariable(), $niveau + 1);
    }

    function affiche_operation($noeud, $niveau) {
        
        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);
    }

    function affiche_parentheses($noeud, $niveau) {
        
    }

    function affiche_preplusplus($noeud, $niveau) {
        
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        $this->affiche($noeud->getVariable(), $niveau + 1);
    }
    
    function affiche_property($noeud, $niveau) {
        
    }

    function affiche_postplusplus($noeud, $niveau) {
        
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        $this->affiche($noeud->getVariable(), $niveau + 1);
    }

    function affiche_rawtext($noeud, $niveau) {
        
    }

    function affiche_sequence($noeud, $niveau) {
        
        $elements = $noeud->getElements();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche_tableau($noeud, $niveau) {
        
        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getIndex(), $niveau + 1);
    }

    function affiche_variable($noeud, $niveau) {
        
    }

    function affiche_token_traite($noeud, $niveau) {
        
    }

    function affiche__while($noeud, $niveau) {
        
        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche_Token($noeud, $niveau) {
        print $noeud."".$noeud->getId()."\n";
        $this->stats++;
    }

}

?>