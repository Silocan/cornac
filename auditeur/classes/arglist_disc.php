<?php

class arglist_disc extends modules {
	protected	$title = "Appels de fonction avec trop d'arguments";
	protected	$description = 'Liste des appels de fonctions avec trop d\'arguments';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array('arglist_def','arglist_call');
	}

	public function analyse() {
        $this->clean_rapport();
        $query = <<<SQL
SELECT NULL, TR1.fichier, TR1.element AS code, TR1.id, '{$this->name}', 0
FROM <rapport> TR1
LEFT JOIN <rapport> TR2
    ON TR2.module='arglist_def' AND
    LEFT(TR1.element, locate('(', TR1.element) - 1) = LEFT(TR2.element, locate('(', TR2.element) -1) AND
    TR1.element = TR2.element
    WHERE TR1.module = 'arglist_call' AND TR2.element IS NULL;

SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>