<?php

class functioncalls extends modules {
    protected $not = false; 
    protected $functions = array();

	function __construct($mid) {
        parent::__construct($mid);
//    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    if (!is_array($this->functions) || empty($this->functions) || empty($this->name) ) {
	        print "Aucune fonction fournie pour ".__CLASS__." : annulation du traitement\n";
	        die();
	    }
	    $in = join("','", $this->functions);
        $this->functions = array();

        if ($this->not) {
            $not = ' not ';
        } else {
            $not = '';
        }
        
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport> 
    SELECT 0, T1.fichier, T2.code AS code, T1.id, '{$this->name}'
    FROM <tokens> T1 
    JOIN <tokens> T2
        ON T2.droite = T1.droite + 1 AND
           T2.fichier = T1.fichier
    WHERE T1.type='functioncall' AND T2.code $not in ('$in')
SQL;
        $this->exec_query($requete);
	}
}

?>