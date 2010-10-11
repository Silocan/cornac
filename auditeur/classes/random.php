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

class random extends functioncalls {
	protected	$title = 'random';
	protected	$description = 'Usage of random function in the code';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
	    $this->functions = get_extension_funcs("rand","array_rand","shuffle","mt_rand",'srand',
	                                           'getrandmax','gmp_random','mt_srand');
	    parent::analyse();
	    
        return true;
	}
}

?>