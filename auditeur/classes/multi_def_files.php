<?php

class multi_def_files extends modules {
	protected	$title = 'Fichiers muli-déclarations';
	protected	$description = 'Fichier définissant plusieurs structures';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL
    CREATE TEMPORARY TABLE multi_def_files
    SELECT DISTINCT fichier FROM <tokens> 
        GROUP BY concat(class,'::', scope) 
        HAVING COUNT(*) > 1
SQL;
        $res = $this->exec_query($query);
    
	    $query = <<<SQL
INSERT INTO <rapport> 
    SELECT NULL, T1.fichier, concat(T1.class,'::', T1.scope), 0,  '{$this->name}' 
    FROM <tokens> T1
    JOIN multi_def_files ON
        T1.fichier = multi_def_files.fichier
    WHERE T1.scope != 'global'
    GROUP BY T1.fichier, concat(T1.class,'::', T1.scope)
SQL;
        $res = $this->exec_query($query);

	    $query = <<<SQL
DROP TABLE multi_def_files
SQL;
        $res = $this->exec_query($query);
        
        return true;
    }
}

?>