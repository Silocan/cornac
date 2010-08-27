<?php

// @todo : centraliser les rquêtes SQL 
// @todo : mettre en parmètre 
abstract class modules {
    protected  $occurrences = 0;
    protected  $fichiers_identifies = 0;
    protected  $total_de_fichiers = 0;
    public static    $mid   = null;
    public static    $table = null;
    
    const FORMAT_DEFAULT = 0;
    const FORMAT_HTMLLIST = 1;
    const FORMAT_DOT = 2;
    const FORMAT_SCOPE = 3;

    protected  $format = modules::FORMAT_HTMLLIST;

    function __construct($mid) {
        global $INI;
        
        if (empty($INI['cornac']['prefix'])) {
            $prefixe = 'tokens';
        } else {
            $prefixe = $INI['cornac']['prefix'];
        }
        
        
        $this->mid = $mid;
        $this->format_export = modules::FORMAT_DEFAULT;
        
        $this->tables = array('<rapport>' => $prefixe.'_rapport',
                              '<rapport_scope>' => $prefixe.'_rapport_scope',
                              '<tokens>' => $prefixe.'',
                              '<tokens_cache>' => $prefixe.'_cache',
                              '<tokens_tags>' => $prefixe.'_tags',
                              '<rapport_module>' => $prefixe.'_rapport_module',
                              '<rapport_dot>' => $prefixe.'_rapport_dot',
                            );

       $this->name = get_class($this);
    }
    
    abstract function analyse();

    function getdescription() {
        return $this->description;
    }

    function gettitle() {
        if (isset($this->title)) {
            return $this->title;
        } else {
            return $this->description.' (old way) ';
        }
    }
    
    function getnombre() {
        return $this->occurrences;
    } 
    
    function init_file() {
        setlocale(LC_TIME, "fr_FR");
        $date = strftime("%A %d %B %Y %H:%M:%S ");
        
        $this->export = "<html><body>
        <table>
            <tr><td><a href=\"index.html\">Index</td><td>&nbsp;</td></tr>
            <tr><td>Production</td><td>$date</td></tr>
            <tr><td>Nombre de fichiers</td><td>{$this->total_de_fichiers}</td></tr>
            <tr><td>Nombre de fichiers identifi&eacute;s</td><td>{$this->fichiers_identifies}</td></tr>
            <tr><td>Nombre d'occurrences</td><td>{$this->occurrences}</td></tr>
        </table>
        <p>&nbsp;</p>
        ";
    }

    function finish_file() {
        $this->export .= "
        <table>
            <tr><td><a href=\"index.html\">Index</td><td>&nbsp;</td></tr>
        </table>
        <p>&nbsp;</p>
</body></html>";
    }
    
    function save_file($name) {
        file_put_contents('export/'.$this->getfilename(), $this->export);
    }

    function getfilename() {
        if ($this->format_export == modules::FORMAT_DOT) {
            return $this->name.".dot";
        } else {
            return $this->name.".html";
        }
    }

    function sauve() {
        if (!isset($this->name)) {
            print "This class has no name (not even the default name!)\n";
            return false;
        }
        if ($this->name == __CLASS__) { 
            print "This class has no name (the default name!)\n";
            return false;
        }
        
        $now = date('c');
        $this->exec_query("REPLACE INTO <rapport_module> VALUES ('$this->name', '$now', '{$this->format}')");

    }

function array2li($array) {
    $retour = '';
    if (count($array) == 0) { 
        $retour .= "Aucune valeur trouvee";
    } else {
        $retour .= "<ul>";
        foreach($array as $name => $fonctions) {
            if (count($fonctions) == 0) { continue; }
            // @a_revoir
            $name = str_replace('/Users/macbook/Desktop/audit/','',$name);
            if (is_array($fonctions)) {
                $retour .= "<li>$name<ul>";
                asort($fonctions);
                foreach($fonctions as $nom => $nombre) {
                    $retour .= "<li>".$this->highlight_code($nom, true)." : $nombre</li>";
                }
                $retour .= "</ul></li>";
            } else {
                $retour .= "<li>$name : $fonctions</li>";
            }
        }
           $retour .= "</ul>";
    }
    
    return $retour;
}

function array_invert($array) {
    $retour = array();    
    
    foreach($array as $key => $value) {
        foreach($value as $k => $v) {
            $retour[$k][] = $key;
        }
    }
    
    return $retour;
}

function highlight_code($code) {
    $code = str_replace("\n",' ', $code);
    $code = highlight_string('<?php '.$code.' ?>', true);
    $code = str_replace('&lt;?php&nbsp;','', $code);
    $code = str_replace('?&gt;','', $code);
    
    
    return $code;
}

function array2dot($points) {
    $retour = '';
    $subgraph = array();

    $occurrences = array();
    foreach($points as $origine => $destinations) {
        $occurrences[] = $origine;
        $occurrences = array_merge($occurrences, array_keys($destinations));
    }
    $occurrences = array_count_values($occurrences);
    
    foreach($points as $origine => $destinations) {
        // @todo : protéger les noms de fichiers
        $subgraph[dirname($origine)][] = $origine;
        foreach($destinations as $dest => $foo) {
            if ($occurrences[$dest] > 1) { 
                $retour .= "\"$origine\" -> \"$dest\";\n";
            }
            $subgraph[dirname($dest)][] = $dest;
        }
    }
    
    $dot = '';
    foreach($subgraph as $dir => $fichiers) {
        $fichiers = array_unique($fichiers);
        $fichiers2 = array();
        foreach($fichiers as $fichier) {
            if ($occurrences[$fichier] == 1) { continue; }
            $fichiers2[] = $fichier;
        }
        $dot .=  "subgraph \"cluster_$dir\" { label=\"$dir\"; \"".join('";"', $fichiers2)."\"; }\n";
    }
    $subgraph = $dot;
    
    $retour = "digraph G {
size=\"8,6\"; ratio=fill; node[fontsize=24];
$retour
$subgraph
}";

    return $retour;
}

    function print_query($query) {
        print $this->prepare_query($query)."\n";
        die();
    }

    function prepare_query($query) {
        $query = str_replace(array_keys($this->tables), array_values($this->tables), $query);
        
        if (preg_match_all('/<\w+>/', $query, $r)) {
            print "Il reste des tables à analyser : ".join(', ', $r[0]);
        }
        
        return $query;
    }
    
    function exec_query($query) {
        $query = $this->prepare_query($query);
        
        $res = $this->mid->query($query);
        $erreur = $this->mid->errorInfo();
        
        if ($erreur[2]) {
            print_r($erreur);
            print $query;
            die();
        }

        return $res;
    }
    
    function dependsOn() {
        return array();
    }
    
    function clean_rapport() {
        $query = <<<SQL
DELETE FROM <rapport> WHERE module='{$this->name}'
SQL;
        $this->exec_query($query);

        $query = <<<SQL
DELETE FROM <rapport_dot> WHERE module='{$this->name}'
SQL;
        $this->exec_query($query);

        $query = <<<SQL
DELETE FROM <rapport_module> WHERE module='{$this->name}'
SQL;
        $this->exec_query($query);
    }
    
    static public function getPHPFunctions() {
        // @todo this depends on PHP used for exécution : we should extract this somewhere else
        $ini = parse_ini_file('../dict/functions2ext.ini', false);
    
        $extras = array('echo','print','die','exit','isset','empty','array','list','unset','eval','dl');

        return array_merge($ini['function'], $extras);
    }
    
    static public function getPHPExtensions() {
        $ini = parse_ini_file('../dict/functions2ext.ini', true);
        
        $exts = array_keys($ini);
        return $exts;
    }

    static public function getPHPClasses() {
        $classes = parse_ini_file('../dict/class2ext.ini', false);
        return $classes['classes'];
    }

    static public function getPHPExtClasses() {
        // @todo this depends on PHP used for exécution : we should extract this somewhere else
        $classes = parse_ini_file('../dict/class2ext.ini', true);
        return $classes;
    }
    
    static public function getPHPGPC() {
        return array('$_GET','$_POST','$_COOKIE','$_REQUEST','$_FILES','$_SESSION',
                     '$HTTP_GET_VARS','$HTTP_POST_VARS','$HTTP_COOKIE_VARS','$HTTP_FILES_VARS', '$HTTP_SESSION_VARS',
                     '$HTTP_RAW_POST_DATA',
                     '$_ENV','$_SERVER',
                     '$HTTP_ENV_VARS','$HTTP_SERVER_VARS',
                     '$http_response_header','$php_errormsg','$argv','$argc',);
    }


    static public function getPHPStandardFunctions() {
        // @todo this depends on PHP used for exécution : we should extract this somewhere else
	    $language_structures = array('echo','print','die','exit','isset','empty','array','list','unset','eval');
	    $ext_array = array('iterator_to_array', 'sqlite_array_query', 'sqlite_fetch_array', 'call_user_func_array', 'call_user_method_array', 'forward_static_call_array', 'is_array', 'array_walk', 'array_walk_recursive', 'in_array', 'array_search', 'array_fill', 'array_fill_keys', 'array_multisort', 'array_push', 'array_pop', 'array_shift', 'array_unshift', 'array_splice', 'array_slice', 'array_merge', 'array_merge_recursive', 'array_replace', 'array_replace_recursive', 'array_keys', 'array_values', 'array_count_values', 'array_reverse', 'array_reduce', 'array_pad', 'array_flip', 'array_change_key_case', 'array_rand', 'array_unique', 'array_intersect', 'array_intersect_key', 'array_intersect_ukey', 'array_uintersect', 'array_intersect_assoc', 'array_uintersect_assoc', 'array_intersect_uassoc', 'array_uintersect_uassoc', 'array_diff', 'array_diff_key', 'array_diff_ukey', 'array_udiff', 'array_diff_assoc', 'array_udiff_assoc', 'array_diff_uassoc', 'array_udiff_uassoc', 'array_sum', 'array_product', 'array_filter', 'array_map', 'array_chunk', 'array_combine', 'array_key_exists');
	    
	    return array_merge($ext_array, $language_structures);
    }
    
    function concat() {
        $args = func_get_args();
        
        global $INI;
        if (isset($INI['mysql']) && $INI['mysql']['active'] == true) {
            return "CONCAT(".join(",", $args).")";
        } elseif (isset($INI['sqlite']) && $INI['sqlite']['active'] == true) {
            return "".join("||", $args)."";
        } else {
            print "Concat isn't defined for this database!";
            die();
        }
    }

    function disabled() {
        print "Module '{$this->name}' is disabled\n";
        return;
    }
}
?>