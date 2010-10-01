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

class loops_oneliner extends modules {
	protected	$title = 'One line loops';
	protected	$description = 'Identify short loops.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array();
	}

	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL
SELECT NULL, T1.fichier, REPLACE(T1.type,'_',''), T1.id, '{$this->name}', 0
FROM dotclear T1
JOIN dotclear T2
    ON T2.fichier = T1.fichier AND
       T2.droite = T1.gauche + 1
WHERE T1.type IN ('_for','_while','_do','_foreach') AND
      T2.line - T1.line < 3
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>