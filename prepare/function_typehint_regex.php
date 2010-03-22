<?php

class function_typehint_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_FUNCTION);
    }
    
    function check($t) {
        if (!$t->hasNext(3)) { return false; }

        $var = $t->getNext(2);
        
        while ($var->checkNotOperateur(')')) {
            if (($var->checkClass('constante') ||
                 $var->checkToken(array(T_ARRAY,T_STRING))) &&
                $var->getNext()->checkClass('variable')) {
                
                if ($var->getNext(1)->checkCode('=') &&
                    $var->getNext(2)->checkNotClass('Token')) {
                        $regex = new modele_regex('affectation',array(0, 1, 2), array(1, 2));
                        Token::applyRegex($var->getNext(), 'affectation', $regex);
    
                        mon_log(get_class($t)." => affectation (".__CLASS__.")");

                        $regex = new modele_regex('typehint',array(0, 1), array(1));
                        Token::applyRegex($var, 'typehint', $regex);
    
                        mon_log(get_class($t)." => typehint (".__CLASS__.")");

                        $var = $var->getNext();
                        continue; 
                } elseif ($var->getNext(1)->checkCode('=')) {
                    if ($var->getNext(3)->checkClass('arglist')) {
                        $regex = new modele_regex('functioncall',array(0, 1), array(1));
                        Token::applyRegex($var->getNext(2), 'functioncall', $regex);
    
                        mon_log(get_class($t)." => affectation (".__CLASS__.")");
                    }
                    
                    $regex = new modele_regex('affectation',array(0, 1, 2), array(1, 2));
                    Token::applyRegex($var->getNext(), 'affectation', $regex);
    
                    mon_log(get_class($t)." => affectation (".__CLASS__.")");

                    $regex = new modele_regex('typehint',array(0, 1), array(1));
                    Token::applyRegex($var, 'typehint', $regex);
    
                    mon_log(get_class($t)." => typehint = (".__CLASS__.")");
                    
                    $var = $var->getNext();
                    continue; 
                } elseif ($var->getNext(1)->checkCode(array(',',')'))) {
                    $regex = new modele_regex('typehint',array(0, 1), array(1));
                    Token::applyRegex($var, 'typehint', $regex);
    
                    mon_log(get_class($t)." => typehint ,) (".__CLASS__.")");
                    
                    $var = $var->getNext();
                    continue; 
                } 
            }
            // cas des typehint avec initialisation
            
            if ($var->checkOperateur('(')) {
                // On veut pas de collision avec une autre structure
                return false; 
            }
            
            if (!$var->hasNext()) { return false; }
            $var = $var->getNext();
        }

        return false;
    }
}

?>