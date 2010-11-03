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

class template_hierarchic extends template {
    protected $root = null;
    
    function __construct($root) {
        parent::__construct();
        
        $this->root = $root;
    }
    
    function affiche($noeud = null, $niveau = 0) {
        if (is_null($noeud)) {
            $noeud = $this->root;
        }
        
        if (!is_object($noeud)) {
            debug_print_backtrace();
            die();
        }
        $class = get_class($noeud);
        $method = "affiche_$class";

        $this->$method($noeud, $niveau + 1);
        if (!is_null($noeud->getNext())){
            $this->affiche($noeud->getNext(), $niveau);
        }
    }
    
    function affiche_arglist($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $elements = $noeud->getList();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche_affectation($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);
    }
    
    function affiche_ternaryop($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getThen(), $niveau + 1);
        $this->affiche($noeud->getElse(), $niveau + 1);
    }

    function affiche_codephp($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getphp_code(), $niveau + 1);
    }

    function affiche_concatenation($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $elements = $noeud->getList();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche_constante($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
    }

    function affiche_functioncall($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";

        $args = $noeud->getArgs();
        $this->affiche($args, $niveau + 1);
    }

    function affiche_inclusion($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $inclusion = $noeud->getInclusion();
        $this->affiche($inclusion, $niveau + 1);
    }

    function affiche_literals($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
    }

    function affiche_operation($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);
    }

    function affiche_parentheses($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
    }

    function affiche_rawtext($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
    }

    function affiche_sequence($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $elements = $noeud->getElements();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche__array($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getIndex(), $niveau + 1);
    }

    function affiche_variable($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
    }

    function affiche_Token($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getCode()." ( Affichage par défaut)\n";
    }

}

?>