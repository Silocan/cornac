<?php

class appelsfonctions extends modules {
	protected	$title = 'Function call through the code';
	protected	$description = 'Appels d\'une fonction par une autre';

	function __construct($mid) {
        parent::__construct($mid);
        
        $this->format = modules::FORMAT_DOT;
	}
	
	public function analyse() {
        $this->clean_rapport();
        
        $in = join("', '", modules::getPHPFunctions()); 
        $concat1 = $this->concat("T1.class","'->'","T1.scope");
        $concat2 = $this->concat("T3.code","'->'","T4.code");
        $query = <<<SQL
INSERT INTO <rapport_dot> 
SELECT $concat1, $concat2, T1.fichier, '{$this->name}'
  from <tokens> T1
  join <tokens_cache> T2 
    on T1.id = T2.id
  join <tokens> T3
    on T1.fichier = T3.fichier AND
       T3.droite = T1.droite + 1 AND
       T3.code != '\$this'
  join <tokens> T4
    on T1.fichier = T4.fichier AND
       T4.droite = T1.droite + 4
where 
 T1.type='method_static' ;
SQL;
        $res = $this->exec_query($query);

        $concat1 = $this->concat("T1.class","'->'","T1.scope");
        $concat2 = $this->concat("T1.class","'->'","T4.code");
        $query = <<<SQL
INSERT INTO <rapport_dot> 
SELECT $concat1, $concat2, T1.fichier, '{$this->name}'
  from <tokens> T1
  join <tokens_cache> T2 
    on T1.id = T2.id
  join <tokens> T3
    on T1.fichier = T3.fichier AND
       T3.droite = T1.droite + 1 AND
       T3.code = '\$this'
  join <tokens> T4
    on T1.fichier = T4.fichier AND
       T4.droite = T1.droite + 4
where 
 T1.type='method' ;
SQL;
        $res = $this->exec_query($query);

        $query = <<<SQL
SELECT T4.code AS methode, T1.class as classe
  from <tokens> T1
  join <tokens_cache> T2 
    on T1.id = T2.id
  join <tokens> T3
    on T1.fichier = T3.fichier AND
       T3.droite = T1.droite + 1 AND
       T3.code != '\$this'
  join <tokens> T4
    on T1.fichier = T4.fichier AND
       T4.droite = T1.droite + 4
where 
 T1.type='method' ;
SQL;
        $res = $this->exec_query($query);
        
        $erreurs = 0;
        $total = 0;
        while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
            $query = <<<SQL
SELECT T1.element
  from <rapport> T1
where 
 T1.module='defmethodes' AND 
 T1.element NOT LIKE "{$ligne["classe"]}->%" AND
 T1.element LIKE "%->{$ligne["methode"]}"

 ;
SQL;
            $res2 = $this->exec_query($query);            
            
            if ($res2->rowCount() == 0) {
                $erreurs++;
            }
            $total++;
        }
        return true;
    }
}

?>