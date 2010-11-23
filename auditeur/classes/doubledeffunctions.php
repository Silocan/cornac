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

class doubledeffunctions extends modules {
	protected	$title = 'Functions being defined twice';
	protected	$description = 'List functions being defined twice, at least. Hopefully, no one will try to use them simultaneously.';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	function dependsOn() {
        return array('deffunctions');	
	}

	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
SELECT NULL, file, TR.element,  TR.token_id, '{$this->name}', 0
FROM <rapport> TR
WHERE module='deffunctions'
GROUP BY element 
HAVING COUNT(*) > 1
SQL;
    
        $this->exec_query_insert('rapport', $query);
        return true;
	}
}

?>