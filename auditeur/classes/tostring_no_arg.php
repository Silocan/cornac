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

class tostring_no_arg extends modules {
	protected	$title = 'tostring without arguments';
	protected	$description = 'Spot __toString methods with arguments (Incompatible change for PHP 5.3)';
	protected	$tags = array('PHP_5.3');

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}

	public function analyse() {
        $this->clean_rapport();

// @todo of course, update this useless query. :)
	    $query = <<<SQL
SELECT NULL, T1.fichier, T1.class, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND
       T2.droite BETWEEN T1.droite AND T1.gauche AND
       T2.type = 'arglist'
LEFT JOIN <tokens> T3
    ON T3.fichier = T1.fichier AND
       T3.droite = T2.droite + 1 AND
       T3.type = 'token_traite'
WHERE T1.type = '_function' AND
      T1.code = '__toString' AND
      T1.class != '' AND
      T3.id IS NULL 
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>