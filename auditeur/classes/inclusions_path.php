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

class inclusions_path extends modules {
	protected	$title = 'Chemin d\'inclusion';
	protected	$description = 'Liste des chemins d\inclusions utilisés (hors __autoload)';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
SELECT NULL, T1.fichier, IFNULL(TC.code, T2.code) AS element, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND
       T2.droite = T1.droite + 1
LEFT JOIN <tokens_cache> TC
    ON TC.id = T2.id
WHERE T1.type='inclusion'
SQL;
        $this->exec_query_insert('rapport', $query);
        
        return true;
	}
}
?>