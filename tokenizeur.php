#!/usr/bin/php 
<?php
//-d xdebug.profiler_enable=On

ini_set('memory_limit',234217728);

$times = array('debut' => microtime(true));
include('prepare/commun.php');
include('prepare/token_traite.php');
include("prepare/analyseur.php");

// ** Capture du nom de l'application à auditer
$application = $ANALYSEUR['application'];

global $FIN; 
// ** Début du travail de collecte des tokens 
$FIN['debut'] = microtime(true);

$path = realpath($application);

// @todo : exporter les informations d'options dans une inclusion
if ($id = array_search( '-h', $argv)) {
    help();
}

// lecture du fichier de .INI pour pouvoir l'utiliser comme valeur par défaut.
if ($id = array_search( '-I', $argv)) {
    $ini = $argv[$id + 1];
    
    unset($argv[$id]);
    unset($argv[$id + 1]);
    
    global $INI;
    if (file_exists('ini/'.$ini)) {
        define('INI','ini/'.$ini);
    } elseif (file_exists('ini/'.$ini.".ini")) {
        define('INI','ini/'.$ini.".ini");
    } elseif (file_exists($ini)) {
        define('INI',$ini);
    } else {
        define('INI','ini/'.'tokenizeur.ini');
    }
} else {
    define('INI','ini/'.'tokenizeur.ini');
}

// @todo : que faire si on ne trouve même pas le .ini ? 
print "Fichier de directives : ".INI."\n";
global $INI;
$INI = parse_ini_file(INI, true);


// lecture des constantes qui peuvent être définies dans le .INI
if ($id = array_search( '-t', $argv)) {
    unset($argv[$id]);
    define('TOKENS',true);
} else {
    define('TOKENS',false);
}

if ($id = array_search( '-T', $argv)) {
    unset($argv[$id]);
    define('TEST',true);
} else {
    define('TEST',false);
}

if ($id = array_search( '-S', $argv)) {
    unset($argv[$id]);
    define('STATS',true);
} else {
    define('STATS',false);
}

// @toto : mettre plusieurs templates en même temps (séparés par des virgules?)
if ($id = array_search( '-g', $argv)) {
    $templates = explode(',', $argv[$id + 1]);

    $templates = array_unique($templates);
    
    foreach ($templates as $i => $template) {
        if (!file_exists('prepare/templates/template.'.$template.'.php')) {
            print "$id) '$template' n'existe pas.\n";
            unset($templates[$i]);
//            $template = 'tree';
        } else {
            print "Utilisation du gabarit ".$template."\n";
        }
    }
    
    if (count($templates) == 0) {
        $templates = array('tree');
    }
    
    
    define('GABARIT',join(',',$templates));
    unset($argv[$id]);
    unset($argv[$id + 1]);
} else {
    define('GABARIT','tree');
    $templates = array('tree');
}

include('prepare/templates/template.php');
foreach ($templates as $id => $template) {
    include('prepare/templates/template.'.$template.'.php');
}

if ($id = array_search( '-l', $argv)) {
    unset($argv[$id]);
    define('LOG',true);
    print "Log actif\n";
} else {
    define('LOG',false);
}

if ($id = array_search( '-i', $argv)) {
    $limite = $argv[$id + 1];
    
    unset($argv[$id]);
    unset($argv[$id + 1]);
    
    print "Cycles = $limite\n";
    
//    $objects = new arrayIterator(array($fichier => $fichier));
//    $scriptsPHP = new PHPFilter($objects);
} else {
    $limite = -1;
}

if ($id = array_search( '-r', $argv)) {
    unset($argv[$id]);
    define('RECURSIVE',true);
    print "mode recursif\n";
} else {
    define('RECURSIVE',false);
}

if ($id = array_search( '-d', $argv)) {
    $dossier = $argv[$id + 1];
    
    if (substr($dossier, -1) == '/') {
        $dossier = substr($dossier, 0, -1);
    }

    if (!file_exists($dossier)) {
        print "Impossible de trouver le dossier '$dossier'\n Annulation\n";
        die();
    }

    print "Travail sur le dossier {$dossier} \n";
    
    $fichiers = glob($dossier.'/*.php');
    $fichiers = array_slice($fichiers, 1, 1);
    
    foreach($fichiers as $fichier) {
        print "./tokenizeur.php -f $fichier -g ".GABARIT. ""." -I ".INI."\n";
        print shell_exec("./tokenizeur.php  -T -i -1 -f $fichier -g ".GABARIT. " "." -I ".INI);
    }
    
    if (RECURSIVE) {
        $fichiers = Liste_directories_recursive($dossier);

        foreach($fichiers as $fichier) {
            $code = file_get_contents($fichier);
            if (strpos($code, '<?') === false) { continue; }
            
            $commande = "./tokenizeur.php -f $fichier -g ".GABARIT." -I ".INI;
            print $commande. "\n";
            print shell_exec($commande);
        }
        die();
    }
    
    die();
} elseif ($id = array_search( '-f', $argv)) {
    $fichier = $argv[$id + 1];
    
    unset($argv[$id]);
    unset($argv[$id + 1]);
    
    print "Travail sur le fichier {$fichier} \n";
    
    if ($id = array_search( '-e', $argv)) {
        unset($argv[$id]);
        
        shell_exec("bbedit $fichier");
    }
    
    $objects = new arrayIterator(array($fichier => $fichier));
    $scriptsPHP = new PHPFilter($objects);

} elseif ($path !== false) {
    print "Travail dans le dossier $path \n";
    // ajouter un système de detection des fichiers
    $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
    $scriptsPHP = new PHPFilter($objects);
} else {
    help();
}

if ($id = array_search( '-v', $argv)) {
    unset($argv[$id]);
    
    define('VERBOSE',true);
} else {
    define('VERBOSE',false);
}

$preparations = array();
    
$extra_cols = array();
foreach($preparations as $p) {
    $cols = $p->get_cols();
    foreach($cols as $nom => $definition) {
        $extra_cols[] = "$nom $definition ";
    }
}
if (count($extra_cols) > 0) {
    $extra_cols = ', '.join(', ', $extra_cols).", ";
} else {
    $extra_cols = '';
}

$tidbits = scandir('prepare');
foreach($tidbits as $module) {
    if ($module[0] == '.') { continue; }
    if ($module == 'token.php') { continue; }
}

$FIN['fait'] = 0;
$FIN['trouves'] = 0;
foreach($scriptsPHP as $name => $object){
    $FIN['trouves']++;
    if ($TOKENIZEUR['verbose'] == 1) {
       print $name."\n";
    }
    if (!file_exists($name)) { 
        print "$name n'existe pas. Omission\n";
        continue;
    }

//
// 4177 est le error_reporting de  E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR (erreurs de compilations seules)
    $exec = shell_exec('php -d short_open_tag=1 -d error_reporting=4177  -l '.$name); // masque trop les erreurs -d error_reporting=4177 
    if (trim($exec) != 'No syntax errors detected in '.$name) {
    //\nNo syntax errors detected in $name
        print "Le script $name n'est pas compilable par PHP\n$exec\n";
        die();
        
    }
    
    global $fichier;
    $fichier = $name;

    $code = file_get_contents($name);
    
    // il faut laisser les <?php et <?xml (et autres) intacts
    // il faut aussi laisser les <?R& (\w\W), qui sont du binaires et pas une PI
    // il faut prendre les <?\s 
    
    if ($c = preg_match_all('/<\\?(?!php)(\w?\s)/is', $code, $r) ) { 
        if (VERBOSE) {
            print "$c corrections de balises ouvrantes\n";
        }
        $code = preg_replace('/<\\?(?!php)(\w?\s)/is', '<?php'."\n".'\1', $code);
    }
    // trop simple, mais devrait marcher sauf pour des binaires (et encore...)
    $code = str_replace('<?=', '<?php echo ', $code);
    
    $brut = @token_get_all($code);
    if (count($brut) == 0) {
        die();
    }
    $nb_tokens_initial = count($brut);
    
    if (TOKENS) {
       print_r($brut);
       $times['fin'] = microtime(true);
       mon_die();
    }
    
    $root = new Token();
    $suite = null;
    
    foreach($brut as $id => $b) {
        $t = new Token();

        $t->setId($id);
        if (is_array($b)) {
            $t->setToken($b[0]);
            $t->setCode($b[1]);
            $t->setLine($b[2]);
        } else {
            $t->setCode($b);
        }
        
        if (is_null($suite)) {
            $suite = $t;
            $root = $t;
        } else {
            $suite->append($t);
            $suite = $suite->getNext();
        }
        unset($brut[$id]);
        
    }

    $t = $root;
    mon_log("\nWSC \n");
    do {
        $t = whitespace::factory($t);
        $t = commentaire::factory($t);
        
        if ($t->getId() == 0 && $t != $root) {
            mon_log("Nouveau Root : ".$t."");
            $root = $t;
        }

        if (VERBOSE) {
            print "$i) ".$t->getCode()."---- \n";
            $template = getTemplate($root, $fichier, 'tree');
            $template['tree']->affiche();
            unset($template);
            print "$i) ".$t->getCode()."---- \n";
       }
    } while ($t = $t->getNext());

    $analyseur = new analyseur();

    $nb_tokens_courant = -1;
    $nb_tokens_precedent = array(-1);

    $i = 0;
    while (1) {
        $i++;
        $t = $root;
        mon_log("\nCycle : ".$i."\n");
        $nb_tokens_precedent[] = $nb_tokens_courant;
        if (count($nb_tokens_precedent) > 3) {
            array_shift($nb_tokens_precedent); 
        }
        $nb_tokens_courant = 0;
        do {
            $t = $analyseur->upgrade($t);
            if (get_class($t) == 'Token') { $nb_tokens_courant++; }
            if ($t->getId() == 0 && $t != $root) {
                mon_log("Nouveau Root : ".$t."");
                $root = $t;
            }

            if (VERBOSE) {
                print "$i) ".$t->getCode()."---- \n";
                $template = getTemplate($root, $fichier, 'tree');
                $template['tree']->affiche();
                unset($template);
                print "$i) ".$t->getCode()."---- \n";
           }
        } while ($t = $t->getNext());
        
        mon_log("Token qui restent : ".$nb_tokens_courant."");
        
        if ($nb_tokens_courant == 0) {
            break 1;
        }
        
        if ($nb_tokens_courant == $nb_tokens_precedent[0] && 
            $nb_tokens_courant == $nb_tokens_precedent[1] &&
            $nb_tokens_courant == $nb_tokens_precedent[2]
            ) { 
            print "Plus d'upgrade au cycle $i \n";

            break 1;
        }
        
        if ($i == $limite) {
            break 1;
        }
    }
    $nb_cycles_final = $i;

    if (TEST) {
        if ($nb_tokens_courant == 0) {
            print "OK\n";
        } else {
            print "Il reste $nb_tokens_courant tokens à traiter\n";
        }
        die();
    }

    if (VERBOSE) {
        if ($nb_tokens_courant == 0) {
            print "Tous les tokens ont été traités\n";
        } else {
            print "Il reste $nb_tokens_courant tokens à traiter\n";
        }
    }

    $token = 0;
    $loop = $root;
    $id = 0;
    while(!is_null($loop)) {
        if (get_class($loop) == "Token") {
            $token++;
        }
        $loop = $loop->getNext();
        $id++;
    }
   
    $templates = getTemplate($root, $fichier);
    foreach($templates as $template) {
        $template->affiche();
        $template->save();
    }

    if (STATS) {
        include('prepare/template.stats.php');
        $template = new template_stats($root);
        $template->affiche();
        
        print $analyseur->verifs." verifications ont ete tentees\n";
        $stats = array_count_values($analyseur->rates);
        asort($stats);
        print_r($stats);
    }
}

$times['fin'] = microtime(true);

$debut = $times['debut'];
unset($times['debut']);
foreach($times as $key => $valeur) {
    $times[$key] = floor(($valeur - $debut) * 1000);
}

mon_die();

function mon_die() {
    global $nb_tokens_courant, $nb_tokens_initial, $fichier, $times, $nb_cycles_final, $limite ;
    
    $message = array();
    $message['date'] = date('r');
    $message['fichier'] = $fichier;
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
    print "Duree : ".number_format(($fin - $FIN['debut']), 2)." s\n";
    print "Fichiers traités : ".$FIN['fait']." \n";
    print "Fichiers trouvés : ".$FIN['trouves']." \n";
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
        $fichier = $this->getInnerIterator()->current();
        $details = pathinfo($fichier);
        
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

function mon_log($message) {
    global $LOG;
    
    if (!LOG) { return true; }
    
    if (!isset($LOG)) {
        $LOG =  fopen('tokenizer.log','w+');
    }
    
    if (!is_resource($LOG)) {
        die("Le fichier de log est mort!\n");
    }
    
    fwrite($LOG, date('r')."\t$message\r");
}

function getTemplate($racine, $fichier, $gabarit = null) {
    if (is_null($gabarit)) {
        $gabarit = GABARIT;
    }
    $templates = explode(',' , $gabarit);
    
    $retour = array();
    foreach($templates as $template) {
        $classe  = "template_".$template;
        $retour[$template] = new $classe($racine, $fichier);
    }
    return $retour;
}

function help() {
    print <<<TEXT
    -d : test all .php files of the folder
    -e : also open the file in an editor
    -f : work on this file
    -g : gabarit à utiliser
    -l : activate log (in the file tokenizer.log)
    -h : This help
    -i : number of cycles. Default to 
    -I : ini file. Default to 'tokenizeur.ini'. 
    -q : quick tests.php file
    -r : mode récursif (avec -d)
    -S : display internal objects stats
    -t : display tokens produced and quit
    -T : activate test mode

TEXT;
    
    die();
}

function Liste_directories_recursive( $path = '.', $level = 0 ){ 
    $ignore = array( 'cgi-bin', '.', '..' ); 
    global $INI;
    if (isset($INI['tokenizeur']['ignore_dirs']) && !empty($INI['tokenizeur']['ignore_dirs'])) {
        $ignore = array_merge($ignore, explode(',',$INI['tokenizeur']['ignore_dirs']));
    }
    
    if (isset($INI['tokenizeur']['ignore_suffixe']) && !empty($INI['tokenizeur']['ignore_suffixe'])) {
        $regex_suffixe = '/('.str_replace(',','|',  preg_quote($INI['tokenizeur']['ignore_suffixe'])).')$/';
    } else {
        $regex_suffixe = '';
    }
    if (isset($INI['tokenizeur']['ignore_prefixe']) && !empty($INI['tokenizeur']['ignore_prefixe'])) {
        $regex_prefixe = '/('.str_replace(',','|',  preg_quote($INI['tokenizeur']['ignore_prefixe'])).')$/';
    } else {
        $regex_prefixe = '';
    }

    $dh = opendir( $path ); 
    $retour = array();
    while( false !== ( $file = readdir( $dh ) ) ){ 
        if( in_array( $file, $ignore ) ){ continue; }
        if( is_dir( "$path/$file" ) ){ 
            $r = Liste_directories_recursive( "$path/$file", ($level+1) ); 
            $retour = array_merge($retour, $r);
        } else { 
            if ($regex_suffixe && preg_match($regex_suffixe, $file)) { continue; }
            if ($regex_suffixe && preg_match($regex_prefixe, $file)) { continue; }
            $retour[] = "$path/$file";
        } 
    } 
     
    closedir( $dh ); 
    return $retour;
} 
?>