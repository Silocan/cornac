<?php

class properties_defined extends modules {
	protected	$description = 'Propriétés définies par une classe';
	protected	$description_en = 'Defined properties by a class';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    	$this->functions = array();
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT 0, T1.fichier, concat(T2.class,'->',T2.code) as code, T2.id, '{$this->name}' 
FROM <tokens> T1
  JOIN <tokens> T2 
  ON T2.fichier = T1.fichier AND 
  T2.droite BETWEEN T1.droite AND T1.gauche 
  AND T2.type='variable'
WHERE T1.class != 'global' AND T1.type='_var';
SQL;
        $this->exec_query($requete);

    // @todo supporter les méthodes / classes
    
    }
}

?>