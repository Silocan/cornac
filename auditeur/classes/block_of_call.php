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

class block_of_call extends modules {
	protected	$title = 'Call blocks';
	protected	$description = 'Successive call to the same function';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_rapport();

// @todo : inclusions to be handler later
// @todo           LEFT(TC1.code, GREATEST(LOCATE('(', TC1.code), LOCATE(' ', TC1.code))) = LEFT(TC3.code, GREATEST(LOCATE('(', TC3.code), LOCATE(' ', TC3.code)))

        $query = <<<SQL
SELECT T1.id AS id1, T2.id AS id2, T3.id AS id3, T1.droite, T3.gauche, TC1.code AS code1, TC2.code AS code2, TC3.code AS code3, T1.fichier, LEFT(TC1.code, LOCATE('(', TC1.code) ) AS code
FROM <tokens> T1
JOIN <tokens> T2 
    ON T1.fichier=  T2.fichier AND T2.droite = T1.gauche + 1
JOIN <tokens> T3
    ON T1.fichier=  T3.fichier AND T3.droite = T2.gauche + 1
JOIN <tokens_cache> TC1 
    ON T1.id=  TC1.id
JOIN <tokens_cache> TC2 
    ON T2.id=  TC2.id
JOIN <tokens_cache> TC3
    ON T3.id=  TC3.id
WHERE T1.type IN ('functioncall') AND 
      T2.type = T1.type AND 
      T3.type = T1.type AND
      LEFT(TC1.code, LOCATE('(', TC1.code)) = LEFT(TC3.code, LOCATE('(', TC3.code) )
ORDER BY T1.id
SQL;
        $res = $this->exec_query($query);
        $resultats = array();
        $already = array();
        
        // @todo : reduce the number of partial list of functions
        while($row = $res->fetch()) {
           if (isset($already[$row['id1']])) {
                continue;
           }

           $resultats[$row['id1']] = array($row['id1'] => $row['code1'],
                                             $row['id2'] => $row['code2'],
                                             $row['id3'] => $row['code3']);
           $already[$row['id1']] = $row['fichier'];
           $already[$row['id2']] = $row['fichier'];
           $already[$row['id3']] = $row['fichier'];

           $id = $row['id3'];
           while ($id > 0) {
               $query2 = <<<SQL
SELECT T2.id, T1.droite, T1.type, T2.type, TC2.code AS code, T1.fichier
FROM <tokens> T1
LEFT JOIN <tokens> T2 
    ON T2.fichier = '{$row['fichier']}' AND T2.droite = T1.gauche + 1
JOIN <tokens_cache> TC2 
    ON T2.id=  TC2.id
WHERE T1.id = {$id} AND
      T1.fichier = '{$row['fichier']}' AND 
      '{$row['code']}' = LEFT(TC2.code, LOCATE('(', TC2.code) )
SQL;
                $res2 = $this->exec_query($query2);
                if ($row2 = $res2->fetch()) {
                   $already[$row2['id']] = $row['fichier'];
                   $resultats[$row['id1']][$row2['id']] = $row2['code'];
                   $id = $row2['id'];
                } else {
                   $id = 0;
                }
             }
         }
        
        foreach($resultats as $resultat) {
            list($id, $code) = each($resultat);
            $code = join("\n", $resultat);
            $query = <<<SQL
INSERT INTO <rapport> VALUES
(NULL, '{$already[$id]}','$code','$id', '{$this->name}', 0 )
SQL;

            $this->exec_query($query);
        }

        return true;
	}
}

?>