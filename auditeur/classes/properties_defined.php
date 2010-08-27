<?php

class properties_defined extends modules {
	protected	$title = 'Propriétés définies';
	protected	$description = 'Propriétés définies par une classe';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $concat = $this->concat("T2.class","'->'","T2.code");
        $query = <<<SQL
INSERT INTO <rapport> 
SELECT NULL, T1.fichier, $concat as code, T2.id, '{$this->name}' 
FROM <tokens> T1
  JOIN <tokens> T2 
  ON T2.fichier = T1.fichier AND 
  T2.droite BETWEEN T1.droite AND T1.gauche 
  AND T2.type='variable'
WHERE T1.class != 'global' AND T1.type='_var';
SQL;
        $this->exec_query($query);

        return true;
    // @todo support methods and classes
    }
}

?>