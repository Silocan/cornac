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

class variables_one_letter extends modules {
	protected	$title = 'One lettre variables';
	protected	$description = 'Variable whose name consists in one letter.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array('variables');
	}

	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
SELECT NULL, TR1.fichier, TR1.element, TR1.id, '{$this->name}', 0
FROM <rapport> TR1
WHERE TR1.module = 'variables' AND LENGTH(REPLACE(TR1.element, '$','')) = 1
GROUP BY BINARY TR1.id;
SQL;
        $this->exec_query_insert('rapport',$query);

        return true;
	}
}

?>