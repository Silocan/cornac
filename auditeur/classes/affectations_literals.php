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

class affectations_literals extends modules {
	protected	$title = 'Assignations de litéraux';
	protected	$description = 'Affectations de valeurs literales';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

// @note affectations that have no variables on the right side (properties, references, list(), noscream...)
        $query = <<<SQL
SELECT NULL, T1.fichier, TC.code, T1.id,  '{$this->name}' , 0 
FROM <tokens> T1
JOIN <tokens_tags> TT1
ON T1.id = TT1.token_id AND TT1.type='right'
JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND T2.id = TT1.token_sub_id
JOIN <tokens> T3
    ON T1.fichier = T3.fichier AND T3.droite BETWEEN T2.droite AND T2.gauche 
JOIN <tokens_cache> TC
    ON TC.id = T1.id
WHERE T1.type = 'affectation' 
GROUP BY T1.id
HAVING SUM(IF(T3.type = 'variable', 1,0)) = 0
SQL;
        $this->exec_query_insert('rapport', $query);    
    }
}

?>