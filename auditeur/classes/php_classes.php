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

class php_classes extends functioncalls {
	protected	$description = 'Liste des classes PHP utilisées';
	protected	$title = 'Classes PHP';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	function dependsOn() {
	    return array('_new');
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $in = join("', '", modules::getPHPClasses());

        $query = <<<SQL
SELECT NULL, T1.fichier, T2.code AS code, T1.id, '{$this->name}', 0
FROM <tokens> T1 
JOIN <tokens> T2
    ON T2.droite = T1.droite + 1 AND
       T2.fichier = T1.fichier
WHERE T1.type='_new' AND 
      T2.code IN ('$in')
SQL;
        $this->exec_query_insert('rapport', $query);
        
        return true;
    }
}

?>