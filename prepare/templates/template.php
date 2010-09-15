<?php


abstract class template {

    function __construct() { }
    
    abstract function affiche($noeud = null, $niveau = 0); 
}

class tree extends template {
    protected $root = null;
    
    function __construct($root) {
        parent::__construct();
        
        $this->root = $root;
    }
    
    function affiche($noeud = null, $niveau = 0) {
        if (is_null($noeud)) {
            $noeud = $this->root;
        }
        
        $class = get_class($noeud);
        $method = "affiche_$class";
        
        if (method_exists($this, $method)) {
            $this->$method($noeud, $niveau + 1);
        } else {
            print "Affichage tree de '".$method."'\n";die;
        }
        if (!is_null($noeud->getNext())){
            $this->affiche($noeud->getNext(), $niveau);
        }
        
        
    }

    function affiche_affectation($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        print str_repeat('  ', $niveau)."droite : \n";
        $this->affiche($noeud->getDroite(), $niveau + 1);
        print str_repeat('  ', $niveau)."gauche : \n";
        $this->affiche($noeud->getGauche(), $niveau + 1);
    }

    function affiche_codephp($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getCode()."\n";
        print str_repeat('  ', $niveau)."code : \n";
        $this->affiche($noeud->getphp_code(), $niveau + 1);
    }

    function affiche_literals($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getCode()."\n";
    }

    function affiche_operation($noeud, $niveau) {
         print str_repeat('  ', $niveau).__CLASS__." \n";
         print str_repeat('  ', $niveau)."droite : \n";
         $this->affiche($noeud->getDroite(), $niveau + 1);
         print str_repeat('  ', $niveau)."operation : ".$noeud->getOperation()."\n";
         print str_repeat('  ', $niveau)."gauche : \n";
         $this->affiche($noeud->getGauche(), $niveau + 1);
    }
    
    function affiche_sequence($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $elements = $noeud->getElements();
        foreach($elements as $id => $e) {
            print str_repeat('  ', $niveau)."$id : \n";
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche_tableau($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getCode()."\n";
        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getIndex(), $niveau + 1);
    }

    function affiche_variable($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getCode()."\n";
    }
    
    function affiche_Token($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getCode()." ( Affichage par défaut)\n";
    }

}

?>