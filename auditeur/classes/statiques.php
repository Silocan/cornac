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

class statiques extends modules {
	protected	$title = 'Statiques';
	protected	$description = 'Liste des statiques (methodes, classes et propriétés)';

    function __construct($mid) {
        parent::__construct($mid);
    }

	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
SELECT NULL, T1.fichier, TC.code,  T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens_cache> TC
    ON TC.id = T1.id 
WHERE type IN ('method_static','property_static','constante_static')
SQL;
        $this->exec_query_insert('rapport', $query);
        
        return true;
	}
	
}

?>