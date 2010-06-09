<?php

class modules_used extends modules {
    protected $not = false; 

	function __construct($mid) {
        parent::__construct($mid);
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        if ($this->not) {
            $not = ' not ';
        } else {
            $not = '';
        }
        
        $this->clean_rapport();

// cas simple : variable -> method
        $requete = <<<SQL
SELECT DISTINCT LOWER(element) FROM caceis_rapport WHERE module='php_functions';
SQL;

        $res = $this->exec_query($requete);
        
        $liste = $res->FetchAll(PDO::FETCH_COLUMN);
        
        foreach(get_loaded_extensions() as $ext) {
            $funcs = get_extension_funcs($ext);
            if (!is_array($funcs))  { continue; }
                
            $f = array_intersect($liste, $funcs);
            if (count($f) > 0) {
                $in = join("', '", $f);
                $requete = <<<SQL
INSERT INTO <rapport> 
SELECT 0, fichier, '$ext', id, '{$this->name}' FROM <rapport> 
WHERE element IN ('$in')
SQL;
                $this->exec_query($requete);

                $liste = array_diff($liste, $f);
                if (count($liste) == 0) { return ; }
            }
        }

        $in = join("', '", $liste);
        $requete = <<<SQL
INSERT INTO <rapport> 
SELECT 0, fichier, 'inconnu', id, '{$this->name}' FROM <rapport> 
WHERE element IN ('$in')
SQL;
        $this->exec_query($requete);
	}
}

?>