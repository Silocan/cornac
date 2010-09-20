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

class ooo_ods_meta {
    private $meta;
    
    function __construct() {
        $this->meta = array(
            'creator' => '',
        );
    }
    
    function setMeta($name, $value) {
        
    }
    
    function asXML() {

        foreach($this->meta as $name => $value) {
            $this->meta[$name] = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
        }
        
        $date = date('r');
        $creator = 'OOO_ODS for PHP (version 0.00a)';
        return     <<<XML
<office:document-meta xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:grddl="http://www.w3.org/2003/g/data-view#" office:version="1.2" grddl:transformation="http://docs.oasis-open.org/office/1.2/xslt/odf2rdf.xsl">
	<office:meta>
		<meta:initial-creator>
			{$this->meta['creator']}
		</meta:initial-creator>
		<meta:creation-date>
			$date
		</meta:creation-date>
		<dc:date>
			$date
		</dc:date>
		<dc:creator>
			{$this->meta['creator']}
		</dc:creator>
		<meta:editing-duration>
			PT00H04M17S
		</meta:editing-duration>
		<meta:editing-cycles>
			1
		</meta:editing-cycles>
		<meta:generator>
			OOO_ODS for PHP (version 0.00a)
		</meta:generator>
		<meta:document-statistic meta:table-count="3" meta:cell-count="2" meta:object-count="0" />
	</office:meta>
</office:document-meta>
XML;
    }
}

?>