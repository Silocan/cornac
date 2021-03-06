<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011 Alter Way Solutions (France)               |
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

class Classes_DoubleDeclaration extends modules {
	protected	$description = 'Classes defined twice';
	protected	$title = 'Classes defined twice : classes defined several times (2 or more)';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	function dependsOn() {
        return array('Classes_Definitions');
	}

	public function analyse() {
        $this->clean_report();

        $query = <<<SQL
SELECT NULL, file, TR.element,  TR.token_id, '{$this->name}', 0
FROM <report> TR
WHERE module='Classes_Definitions'
GROUP BY element 
HAVING COUNT(*) > 1
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>