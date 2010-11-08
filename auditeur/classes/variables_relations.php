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

class variables_relations extends modules {
	protected	$title = 'Link between variables';
	protected	$description = 'Linked variables : when two variables are in the same instructures ($x = $a + $b), then, they are in relation.';

	function __construct($mid) {
        parent::__construct($mid);
        
        $this->format = modules::FORMAT_DOT;
	}
	
	public function analyse() {
        $this->clean_rapport();

// @todo : this should be done context by context. How can I do that? 
// @note I need another table for this        
        $query = <<<SQL
INSERT INTO <rapport_dot>
SELECT  T4.code, T2.code, CONCAT(T1.class,'::',T1.scope), '{$this->name}' 
FROM <tokens> T1
JOIN <tokens_tags> TT1
    ON T1.id = TT1.token_id AND TT1.type='left'
JOIN <tokens> T2
    ON T2.id = TT1.token_sub_id AND T2.type='variable' AND T1.fichier =T2.fichier
JOIN <tokens_tags> TT2
    ON T1.id = TT2.token_id AND TT2.type='right'
JOIN <tokens> T3
    ON T3.fichier = T1.fichier AND T3.id = TT2.token_sub_id
JOIN <tokens> T4
    ON T4.fichier = T1.fichier AND T4.droite BETWEEN T3.droite AND T3.gauche AND T4.type='variable'
WHERE T1.type = 'affectation'; 
SQL;
        $this->exec_query($query);

        return true;
	}
}

?>