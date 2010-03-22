<?php

class arglist_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('(');
    }
 
    function check($t) {
        if (!$t->hasPrev(  )) { return false; }

        // pour que ce soit un appel de fonction, il faut ca avant. 
        if ($t->getPrev()->checkNotFunction() &&
            $t->getPrev()->checkNotClass(array('variable','tableau')) &&
            $t->getPrev()->checkNotCode('}')) { return false;}
        
       if ($t->getPrev()->checkCode('}') && 
        // cas du getPrev(1) ? 
        $t->getPrev(2)->checkNotCode('{')) {
                return false;
            }
            
        $var = $t->getNext(); 
        $this->args   = array();
        $this->remove = array();
        
        $pos = 1;
        
        while ($var->checkNotClass('Token') && $var->checkNotCode(')') &&
               $var->getNext()->checkCode(',')) {
            $this->args[]    = $pos;
            $this->remove[]  = $pos;
            $this->remove[]  = $pos + 1;
            
            $pos += 2;
            $var = $var->getNext();
            if ($var->checkCode('(')) { return false; }
            $var = $var->getNext();
            if ($var->checkCode('(')) { return false; }
        }

        if ($var->checkCode(')')) {
            // cas des conditions ? : 
            $this->remove[] = $pos; // le ) finale
            
            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } elseif ($var->getNext()->checkCode(')')) {
            if ($var->checkClass('Token')) { return false; }

            $this->args[]    = $pos ;

            $this->remove[]  = $pos ;
            $this->remove[]  = $pos + 1;

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } else {
            $this->args = array();
            $this->remove = array();
            return false;
        }
    }
}
?>