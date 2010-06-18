<?php

class properties_used extends modules {
	protected	$description = 'Propriétés utilisées par une classe';
	protected	$description_en = 'Used properties';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    	$this->functions = array();
	}
	
	public function analyse() {
        $this->clean_rapport();


// this are the properties used within the class : we rely on $this
        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT 0, T1.fichier, CONCAT(T2.class,'->',T3.code) AS code, T2.id, '{$this->name}' 
FROM <tokens> T1
  JOIN <tokens_tags> TT 
    ON TT.token_id = T1.id AND TT.type='object' 
  JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND TT.token_sub_id = T2.id AND T2.code='\$this'
  JOIN <tokens_tags> TT2
    ON TT2.token_id = T1.id AND TT2.type='property'
  JOIN <tokens> T3
    ON T1.fichier = T3.fichier AND TT2.token_sub_id = T3.id
  WHERE T1.type='property';
SQL;
        $this->exec_query($requete);


// this are the other properties used within the class : we don't know what to do now 
        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT 0, T1.fichier, CONCAT(T2.code,'->',T3.code) AS code, T2.id, '{$this->name}' 
FROM <tokens> T1
  JOIN <tokens_tags> TT 
    ON TT.token_id = T1.id AND TT.type='object' 
  JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND TT.token_sub_id = T2.id AND T2.code!='\$this'
  JOIN <tokens_tags> TT2
    ON TT2.token_id = T1.id AND TT2.type='property'
  JOIN <tokens> T3
    ON T1.fichier = T3.fichier AND TT2.token_sub_id = T3.id
  WHERE T1.type='property';
SQL;
        $this->exec_query($requete);
    
    }
}

?>