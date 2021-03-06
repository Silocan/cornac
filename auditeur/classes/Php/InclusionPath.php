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

class Php_InclusionPath extends modules {
	protected	$title = 'Inclusion path';
	protected	$description = 'List of identified list paths, in inclusions()';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_report();

        $query = <<<SQL
SELECT NULL, T1.file, IFNULL(TC.code, T2.code) AS element, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T1.file = T2.file AND
       T2.left = T1.left + 1
LEFT JOIN <tokens_cache> TC
    ON TC.id = T2.id
WHERE T1.type='inclusion'
SQL;
        $this->exec_query_insert('report', $query);
        
        return true;
	}
}
?>