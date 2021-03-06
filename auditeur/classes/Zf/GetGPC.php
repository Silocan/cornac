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

class Zf_GetGpc extends modules {
	protected	$title = 'ZF : incoming values from HTTP request';
	protected	$description = 'Methods from ZF controllers that gives access to GPC values';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT NULL, T1.file, T1.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens_tags> TT
    ON TT.token_sub_id = T1.id
WHERE
    T1.code in ('getRequest',
                'getPost',
                'getParams',
                'getParam',
                'isErrors',
                'isValid',
                'isPost',
                'getModuleName',
                'getControllerName',
                'getActionName',
                'getParameterValue',
                '_getParams',
                '_getParam',
                '_getAllParams') AND 
    TT.type='function'
SQL;

    $this->exec_query_insert('report', $query);
	}
}

?>