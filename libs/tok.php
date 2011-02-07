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

function mon_die() {
    global $nb_tokens_courant, $nb_tokens_initial, $file, $times, $nb_cycles_final, $limite ;
    
    $message = array();
    $message['date'] = date('r');
    $message['file'] = $file;
    $message['tokens'] = $nb_tokens_initial;
    $message['reste'] = $nb_tokens_courant;
    $message['nb_cycles'] = $nb_cycles_final;
    $message['nb_cycles_autorise'] = $limite;
    $message['fin'] = $times['fin'];
    $message['memoire_max'] = memory_get_peak_usage();
    $message['memoire_finale'] = memory_get_usage();
    
    $message = join("\t", $message)."\n";
    
    $fp = fopen('analyseur.log','a');
    fwrite($fp, $message);
    fclose($fp);
    
    die();
}

function termine() {
    global $FIN;
    
    $fin = microtime(true);
    
    print "================================================\n";
    print "Duration : ".number_format(($fin - $FIN['debut']), 2)." s\n";
    print "Processed files : ".$FIN['fait']." \n";
    print "Found files : ".$FIN['trouves']." \n";
    die();
}

class PHPFilter extends FilterIterator 
{
    private $userFilter;
    public $nb;
    
    public function __construct(Iterator $iterator  )
    {
        parent::__construct($iterator);
        $this->nb = 0;
    }
    
    public function accept()
    {
    	$this->nb++;
        $file = $this->getInnerIterator()->current();
        $details = pathinfo($file);
        
        if( strpos($details['dirname'].'/', '/fckeditor/') !== false) {
            return false;
        }

        if( strpos($details['dirname'].'/', '/cssTidy/') !== false) {
            return false;
        }
        
         if( strpos($details['dirname'].'/', '/Minify/') !== false) {
            return false;
        }

         if( strpos($details['dirname'].'/', '/pear/') !== false) {
            return false;
        }

         if( strpos($details['dirname'].'/', '/jscalendar/') !== false) {
            return false;
        }

         if( strpos($details['dirname'].'/', '/jpgraph/') !== false) {
            return false;
        }

         if( strpos($details['dirname'].'/', '/fpdf/') !== false) {
            return false;
        }

         if( strpos($details['dirname'].'/', '/exif/') !== false) {
            return false;
        }
        
         if( strpos($details['dirname'].'/', '/html2pdf/') !== false) {
            return false;
        }
        
         if( strpos($details['dirname'].'/', '/fpdi/') !== false) {
            return false;
        }

         if( strpos($details['dirname'].'/', '/fonts/') !== false) {
            return false;
        }

         if( strpos($details['dirname'].'/', '/exif/') !== false) {
            return false;
        }


        if( strpos($details['dirname'], 'cligraphcrm_0.991/include') !== false) {
            return false;
        }

        if( strpos($details['dirname'], 'cligraphcrm_0.991/fonts') !== false) {
            return false;
        }

        if( strpos($details['dirname'], 'cligraphcrm_0.991/etat') !== false) {
            return false;
        }
        
        if( strpos($details['dirname'], 'cligraphcrm_0.991/themes') !== false) {
            return false;
        }

        if( isset($details['extension'] ) && ($details['extension'] == 'php' || $details['extension'] == 'inc' || $details['extension'] == 'dao' || $details['extension'] == 'lib') ) {
            return true;
        }
        return false;
    }
}

function getTemplate($racine, $file, $gabarit = null) {
    if (is_null($gabarit)) {
        global $INI;
        $gabarit = $INI['templates'];
    }
    $templates = explode(',' , $gabarit);
    
    $return = array();
    foreach($templates as $template) {
        $class = "template_".$template;
        if (!class_exists($class, false)) {
            include('prepare/templates/template.'.$template.'.php');
        }
        $return[$template] = new $class($racine, $file);
    }
    return $return;
}

function liste_directories_recursive( $path = '.', $level = 0 ){ 
    return liste_directory($path, $level, true);
}

function liste_directories( $path = '.', $level = 0, $recursive = false ){ 
    global $INI;

    $ignore_dirs = array( 'cgi-bin', '.', '..',
                          'CVS','.svn','.git','.hg', '.bzr', // @todo : mercurial? other vcs's special folder : please add 
                          'adodb','fpdf','fckeditor','incutio','lightbox','nusoap','odtphp','pear','phpthumb','phputf8','scriptaculous','simpletest','smarty','spyc','tiny_mce','tinymce'); 
    if (isset($INI['tokenizeur']['ignore_dirs']) && !empty($INI['tokenizeur']['ignore_dirs'])) {
        $ignore_dirs = array_merge($ignore_dirs, explode(',',$INI['tokenizeur']['ignore_dirs']));
    } else {
        // @emptyelse
    }

    if (isset($INI['tokenizeur']['ignore_suffixe']) && !empty($INI['tokenizeur']['ignore_suffixe'])) {
        $regex_suffix = str_replace(',','|',  preg_quote($INI['tokenizeur']['ignore_suffixe']));
        $regex_suffix = '/('.$regex_suffix.')$/i';
    } else {
        $regex_suffix = array('.gif','.jpg','.jpeg','.xsl','.css','.js','.png');
        $regex_suffix = '/('.join('|', $regex_suffix).')$/i';
    }

    if (isset($INI['tokenizeur']['ignore_prefixe']) && !empty($INI['tokenizeur']['ignore_prefixe'])) {
        $regex_prefix = str_replace(',','|',  preg_quote($INI['tokenizeur']['ignore_prefixe']));
        $regex_prefix = '/('.$regex_prefix.')$/';
    } else {
        $regex_prefix = array('\\.');
        $regex_prefix = '/^('.join('|', $regex_prefix).')/';
    }

    $return = array();

    $dh = opendir( $path ); 
    if (!$dh) {  
        print "Couldn't open $path.\n"; 
        return $return; 
    }
    while( false !== ( $file = readdir( $dh ) ) ){ 
        if( in_array( $file, $ignore_dirs ) ){ continue; }
        if( is_dir( "$path/$file" ) ){ 
            if ($recursive) {
                $r = liste_directories( "$path/$file", ($level+1), $recursive ); 
                $return = array_merge($return, $r);
            } // @emptyelse ignore 
        } else {
            // @note files without extensions are usually not interesting.
            if (strpos($file,'.') === false) { continue; }
            // @doc remove matching suffix (aka, extensions)
            if ($regex_suffix && preg_match($regex_suffix, $file)) { continue; }
            // @doc remove matching prefix (., probably)
            if ($regex_prefix && preg_match($regex_prefix, $file)) { continue; }
            // @doc The rest is accepted, until we find a PHP tag in it (see later)
            $return[] = "$path/$file";
        } 
    } 
     
    closedir( $dh ); 
    return $return;
} 

?>