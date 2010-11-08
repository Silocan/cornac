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

class zfClasses extends modules { 
	protected	$title = 'Zend Framework classes';
	protected	$description = 'Repère les classes issues du Zend Framework';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}
	
	public function analyse() {
        $this->clean_rapport();

        $list = modules::getZendFrameworkClasses();
        $in = "'".join("', '", $list)."'";
        
        // @note classes extended
	    $query = <<<SQL
SELECT NULL, T1.fichier, T2.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens_tags> TT 
    ON TT.token_id = T1.id AND
       TT.type = 'extends'
JOIN <tokens> T2
    ON TT.token_sub_id = T2.id AND
       T1.fichier = T2.fichier AND 
       T2.code IN ($in)
WHERE T1.type='_class'; 
SQL;
        $this->exec_query_insert('rapport', $query);
        
        return true;
	}
}

?>