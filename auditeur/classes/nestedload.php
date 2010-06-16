<?php

class nestedload extends modules {
	protected	$description = 'Liste des boucles contenant un appel de fonctin lourd';
	protected	$description_en = 'List of classes et its extensions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    	$this->functions = array();
	}
	
	public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport>
SELECT 0, T1.fichier, T2.code, T1.id, '{$this->name}' 
    FROM <tokens> T1
    JOIN <tokens> T2
        ON T1.fichier = T2.fichier AND T2.droite BETWEEN T1.droite AND T1.gauche
    WHERE T1.type in ('_while','_for','_foreach') AND T2.type IN  ('token_traite')
    GROUP BY T1.fichier, T1.droite, T2.type
    HAVING COUNT(*) > 1
SQL;

        $this->exec_query($requete);

	}
}

?>