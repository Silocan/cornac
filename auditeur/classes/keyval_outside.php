<?php 

class keyval_outside extends modules {
	protected	$title = 'Clé/valeurs hors de la boucle';
	protected	$description = 'Identifie les clé/valeur qui sont hors de la boucle de leur initialisation.';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('keyval');
	}
	
	public function analyse() {
        $this->clean_rapport();

// @rfu SELECT TR1.element, T1.droite, T1.gauche, T2.droite, T1.fichier, T1.ligne, T2.ligne

	    $query = <<<SQL
INSERT INTO <rapport>
SELECT NULL, T1.fichier, T2.code, T2.id, '{$this->name}',0
FROM cornac_rapport TR1
JOIN cornac T1 
    ON T1.id = TR1.token_id
JOIN cornac T2
    ON T1.fichier = T2.fichier AND
       T1.class = T2.class AND
       T1.scope = T2.scope AND
       T2.code = TR1.element AND
       T2.droite > T1.gauche
WHERE TR1.module='keyval';
SQL;
        $this->exec_query($query);
        
        return true;
	}
}

?>