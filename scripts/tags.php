#!/usr/bin/php 
<?php

// @todo merge comments nexting each other

include('../libs/getopts.php');

$args = $argv;
if (get_arg($args, '-f')) { define('SHOW_FILES','true'); }
if (get_arg($args, '-d')) { define('SHOW_DIRS','true'); }
if (get_arg($args, '-e')) { define('SHOW_DIRS','true'); }

if ($format = get_arg_value($args, '-F', 'print_r')) { 
    if (!in_array($format, array('print_r','csv','xml'))) { $format = 'print_r'; }
    define('FORMAT', $format); 
}
if ($dir = get_arg_value($args, '-D', '.')) {
    if (!file_exists($dir)) { print "'$dir' doesn't exist\n"; die(); }
    define('DIR', $dir);  
}

chdir($dir);

$liste = Liste_directories_recursive('.');

// @question : qoui?
# @autre : non?

//$liste = array('./tags.php');
//$fichiers = $tags = array();
$comments = array();
foreach($liste as $fichier) {
    $php = file_get_contents($fichier);
    $tokens = token_get_all($php);
    foreach($tokens as $token) {
        if (is_array($token) && $token[0] == T_COMMENT) {
            $comment = remove_delimiter($token[1]);
            
            if (preg_match_all('/(@[a-zA-Z0-9_\-]+)/is', $comment, $r)) {
                $token['tags'] = $r[1];
                if (in_array('@_', $token['tags'])) {
                // comments are ignored
                    continue; 
                }
            } else {
                $token['tags'] = array('@no_tag');
            }
                $token['comment'] = preg_replace('/(@[a-zA-Z0-9_\-]+)/is','', $comment);
                $token['fichier'] = $fichier;
                unset($token[0]);
                $token['ligne'] = $token[2];
                unset($token[2]);
                $token['raw'] = $comment;
                unset($token[1]);
                
                $comments[] = $token;
        }
    }
}

chdir(dirname(__FILE__));

export_html($comments);
export_tags_html($comments);
export_csv($comments);

//print_r($tags);
//print_r($fichiers);
die();

function export_csv($comments) {
    $csv = "";
    
    $fp = fopen('tags.csv', 'w+');
    
    foreach($comments as $comment) {
        $comment['tags'] = join(', ', $comment['tags']);
        unset($comment['raw']);
        fputcsv($fp, $comment);
    }
    fclose($fp);  
//    print file_put_contents('tags.csv', $html)." octets écrits\n";
}

function export_html($comments) {
    setlocale(LC_TIME, "fr_FR");
    $date = strftime("%A %d %B %Y %T" );

    $html = <<<HTML
<html>
    <head></head>
    <body>
    <p>Generated on : $date</p>
HTML;

    $fichiers = array();
    foreach($comments as $id => $comment) {
        $fichiers[$comment['fichier']][] = $comment;
    }


    $html .= "<table border=1>";
    foreach($fichiers as $fichier => $commentaires) {
        $html .= "<tr><td colspan=\"3\"><a name=\"".make_anchor($fichier)."\"><b>".htmlentities($fichier)."</b></td></tr>\n";
        foreach ($commentaires as $id => $commentaire) {
            $html .= "<tr>
    <td>$id)</td>
    <td>".htmlentities($commentaire['comment'])."</td>
    <td><a href=\"tags.html#".make_anchor($commentaire['tags'][0])."\">".htmlentities($commentaire['tags'][0])."</a></td>    
    </tr>\n";
        }
    }
    $html .= "</table>";
    $html .= <<<HTML
    </body>
</html>
HTML;

    print file_put_contents('files.html', $html)." octets écrits\n";
}

function export_tags_html($comments) {
    setlocale(LC_TIME, "fr_FR");
    $date = strftime("%A %d %B %Y %T");

    $html = <<<HTML
<html>
    <head></head>
    <body>
    <p>Generated on : $date</p>
HTML;

    $tags = array();
    foreach($comments as $id => $comment) {
        foreach($comment['tags'] as $tag) {
            $tags[$tag][] = $comment;
        }
    }

    $html .= "<table border=1>";
    foreach($tags as $tag => $commentaires) {
        $html .= "<tr><td colspan=\"3\"><a name=\"".make_anchor($tag)."\"><b>".htmlentities($tag)."</b></td></tr>\n";
        foreach ($commentaires as $id => $commentaire) {
            $html .= "<tr>
    <td>$id) </td>
    <td>".htmlentities($commentaire['comment'])."</td>
    <td><a href=\"files.html#".make_anchor($commentaire['fichier'])."\">".htmlentities($commentaire['fichier'])."</a></td>    
    </tr>\n";
        }
    }
    $html .= "</table>";
    $html .= <<<HTML
    </body>
</html>
HTML;

    print file_put_contents('tags.html', $html)." octets écrits\n";
}
function remove_delimiter($comment) {
    $comment = trim($comment);
    
    if ($comment[0] == '#') {
        $comment = substr($comment, 1);
    } elseif (substr($comment, 0, 2) == '//') {
        $comment = substr($comment, 2);    
    } elseif (substr($comment, 0, 2) == '/*') {
        $comment = substr($comment, 2, -2);
    } else {
        print "Commentaire inconnu : $comment\n";
    }
    $comment = trim($comment);
    return $comment;
}

$dirs = array_map('dirname', $liste);
$dirs = array_count_values($dirs);
if (SHOW_DIRS) { display($dirs); }

$files = array_map('basename', $liste);
$files = array_count_values($files);
if (SHOW_FILES) { display($files); }

$exts = array_map('cb_exts', $liste);
$exts = array_count_values($exts);
if (SHOW_EXTS) { display($exts); }

//print count($liste)." fichiers distincts\n";

function liste_directories_recursive( $path = '.', $level = 0 ){ 
    $ignore = array( 'cgi-bin', '.', '..' ); 

    $dh = opendir( $path ); 
    if (!is_resource($dh)) { return array(); }
    $retour = array();
    while( false !== ( $file = readdir( $dh ) ) ){ 
        if( !in_array( $file, $ignore ) && $file[0] != '.'){ 
            if( is_dir( "$path/$file" ) ){ 
                $r = Liste_directories_recursive( "$path/$file", ($level+1) ); 
                $retour = array_merge($retour, $r);
            } else { 
                $retour[] = "$path/$file";
            } 
        } 
    } 
     
    closedir( $dh ); 
    return $retour;
} 

function make_anchor($name) {
    $name = preg_replace('/[^a-zA-Z]/', '_', $name);
    $name = preg_replace('/_+/', '_', $name);
    $name = trim($name, '_');
    return $name;
}

function cb_exts($filename) {
    $filename = basename($filename);
    $pos = strrpos($filename, '.');
    return substr($filename, $pos); 
}

function display($list) {
    if (FORMAT == 'print_r') {
        print_r($list);
    } elseif (FORMAT == 'xml') {
        print "<list>\n    <file>".join("</file>\n    <file>", $list)."</file>\n</list>\n";
    } elseif (FORMAT == 'csv') {
        print '"'.join("\"\n\"", $list).'"';
    } 
    
    return true;
}
?>