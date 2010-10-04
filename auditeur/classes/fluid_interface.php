<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Damien Seguy <damien.seguy@gmail.com>                        |
   +----------------------------------------------------------------------+
 */

class fluid_interface extends modules {
	protected	$title = 'Interfaces fluides';
	protected	$description = 'Ceci est l\'analyseur fluid_interface par défaut. ';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array();
	}
	
// @todo put a name in this element
	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL
SELECT T1.id, T1.fichier, count(*) AS nb
FROM <tokens> T1
JOIN <tokens> T2
ON T1.fichier = T2.fichier AND
   T2.type = 'method' AND
   T2.droite BETWEEN T1.droite AND T1.gauche
LEFT JOIN <tokens> T3
ON T1.fichier = T3.fichier AND
   T3.type = 'method' AND
   T3.droite = T1.droite - 1
WHERE T1.type = 'method' AND
      T3.type IS NULL 
GROUP BY T1.id
HAVING nb > 1;
SQL;
        $res = $this->exec_query($query);
        
        //
        while($row = $res->fetch(PDO::FETCH_ASSOC)) {
    	    $query = <<<SQL
SELECT NULL, T1.fichier, CONCAT(T1.code, '->', GROUP_CONCAT(T4.code ORDER BY T4.droite  SEPARATOR '->')), T1.id, '{$this->name}', 0
    FROM <tokens> T1
    JOIN <tokens> T2 
        ON T2.fichier = T1.fichier AND
           T2.droite BETWEEN T1.droite + 1 AND T1.droite + {$row['nb']}
    JOIN <tokens_tags> TT
        ON TT.token_id = T2.id AND
           TT.type='method'
    JOIN <tokens> T3
        ON T3.fichier = T2.fichier AND
           T3.id = TT.token_sub_id
    JOIN <tokens> T4
        ON T4.fichier = T1.fichier AND
           T4.droite = T3.droite + 1
    WHERE T1.id = {$row['id']}
SQL;
            $this->exec_query_insert('rapport', $query);
        }
        
        return true;
	}
}

?>