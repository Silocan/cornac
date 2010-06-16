<?php

class arobases extends modules {
    protected    $description = 'Utilisation des arobases';
    protected    $description_en = 'Usage of @';

    function __construct($mid) {
        parent::__construct($mid);
        
        $this->name = __CLASS__;
    }
    
    public function analyse() {
        $this->clean_rapport();

        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT 0, TC.fichier, TC.code AS code, T1.id, '{$this->name}'
    FROM <tokens> T1
    LEFT JOIN <tokens_cache>  TC 
    ON T1.id = TC.id 
    WHERE T1.type='noscream' 
SQL;
        print $this->prepare_query($requete);
        $this->exec_query($requete);
    }
}

?>