<?php

class gpc_affectations extends modules {
	protected	$title = 'Assignations de GPC';
	protected	$description = 'Liste des variables GPC qui se voient assigné une valeur (mauvaise pratique)';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array('affectations_variables');
	}

	public function analyse() {
        $this->clean_rapport();

        $gpc_regexp = '(\\\\'.join('|\\\\',modules::getPHPGPC()).')';

        $query = <<<SQL
SELECT NULL, TR1.fichier, TR1.element, TR1.id, '{$this->name}', 0
FROM <rapport> TR1
WHERE TR1.module = 'affectations_variables' AND 
      TR1.element REGEXP '^$gpc_regexp'
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>