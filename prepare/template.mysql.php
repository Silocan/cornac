<?php

class template_mysql extends template {
    protected $root = null;
    private $mysql = null;
    private $ligne = 0;
    private $scope = 'global';
    private $class = '';
    
    private $table = 'tokens';
    private $tags = array();
    
    function __construct($root, $fichier = null) {
        parent::__construct();
        
        global $INI;
        
        $this->host = '127.0.0.1';
        $this->user = 'root';
        $this->mdp = '';
        $this->dbname = 'analyseur';
        $this->table = $INI['template.mysql']['table'] ?: 'tokens';
        $this->table_tags = $this->table.'_tags';

        $this->mysql = new pdo("mysql:dbname={$this->dbname};host={$this->host}",$this->user,$this->mdp);

        $this->mysql->query('DELETE FROM '.$this->table.' WHERE fichier = "'.$fichier.'"');
        $this->mysql->query('CREATE TABLE '.$this->table.' (id       INT UNIQUE PRIMARY KEY AUTO_INCREMENT, 
                                                          droite   INT UNSIGNED, 
                                                          gauche   INT UNSIGNED,
                                                          type     CHAR(20),
                                                          code     VARCHAR(255),
                                                          fichier  VARCHAR(255) DEFAULT "prec",
                                                          ligne    INT,
                                                          scope    VARCHAR(255),
                                                          class    VARCHAR(255),
                                                          PRIMARY KEY (`id`),
                                                          UNIQUE KEY `id` (`id`),
                                                          KEY `fichier` (`fichier`),
                                                          KEY `type` (`type`),
                                                          KEY `droite` (`droite`),
                                                          KEY `gauche` (`gauche`)
                                                          
                                                          )');

        $this->mysql->query('CREATE TABLE '.$this->table_tags.' (
  `token_id` int(10) unsigned NOT NULL,
  `token_sub_id` int(10) unsigned NOT NULL,
  `type` varchar(50) NOT NULL,
  KEY `token_id` (`token_id`),
  KEY `token_sub_id` (`token_sub_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1');

        $this->mysql->query('delimiter //');
        $this->mysql->query('CREATE TRIGGER auto_tag BEFORE DELETE ON `tokens`
FOR EACH ROW
BEGIN
DELETE FROM tokens_tags WHERE token_id = OLD.id OR token_sub_id = OLD.id;
END;
//');
        $this->mysql->query('delimiter ;');
        
        $this->root = $root;

    }
    
    function save($filename = null) {
        print "Sauvé en base\n";
    }
    
    function affiche($noeud = null, $niveau = 0) {
        if ($niveau > 200) {
            print_r(xdebug_get_function_stack());        
            print "Attention : plus de 100 niveaux de récursion (annulation)\n"; die();
        }
        if (is_null($noeud)) {
            if ($niveau == 0) {
                $noeud = $this->root;
            } else {
                print_r(xdebug_get_function_stack());        
                print "On a tenté de refiler un null à affiche.";
                die();
            }
        }
        
        if (!is_object($noeud)) {
            print_r(xdebug_get_function_stack());        
            print "Attention, $noeud n'est pas un objet (".gettype($noeud).")\n";
            die();
        }
        $class = get_class($noeud);
        $method = "affiche_$class";
        
        if (method_exists($this, $method)) {
            $retour = $this->$method($noeud, $niveau + 1);
        } else {
            print "Affichage ".__CLASS__." de '".$method."'\n";die;
        }
        if (!is_null($noeud->getNext())){
            print "GetNext()\n";
            $this->affiche($noeud->getNext(), $niveau);
        }

        return $retour;
    }
    
////////////////////////////////////////////////////////////////////////
// mysql functions
////////////////////////////////////////////////////////////////////////

    private static $ids = 0;
    
    function getNextId() {
        return $this->ids++;
    }

    private static $intervallaire = 0;
    
    function getIntervalleId() {
        return $this->intervallaire++;
    }

    function saveNoeud($noeud) {
        global $fichier;
        
        if (($noeud->getligne() + 0) > 0) {
            $this->ligne = $noeud->getligne() + 0;
        } 
        
        $requete = "INSERT INTO {$this->table} VALUES 
            (0 ,
             '".$noeud->myDroite."',
             '".$noeud->myGauche."',
             '".get_class($noeud)."',
             ".$this->mysql->quote($noeud->getCode()).",
             '$fichier',
             ". $this->ligne .",
             '". $this->scope ."',
             '". $this->class ."'
             )";
             

        $this->mysql->query($requete);
        if ($this->mysql->errorCode() != 0) {
            print $requete."\n";
            print_r($this->mysql->errorInfo());
            die();
        }
        
        $retour = $this->mysql->lastinsertid();
        
        if (is_array($this->tags) && count($this->tags) > 0) {
            foreach($this->tags as $label => $tokens) {
                foreach($tokens as $token) {
                    $requete = "INSERT INTO {$this->table_tags} VALUES 
                    ($retour ,
                     '".$token."',
                     '".$label."')";
    
                    $this->mysql->query($requete);
                    if ($this->mysql->errorCode() != 0) {
                        print $requete."\n";
                        print_r($this->mysql->errorInfo());
                        die();
                    }
                }
            }
        }
        
        $this->tags = array();
        
        return $retour;
    }

////////////////////////////////////////////////////////////////////////
// mysql functions
////////////////////////////////////////////////////////////////////////
    function affiche_token_traite($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_affectation($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_arginit($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');
        
        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getValeur(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_arglist($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $elements = $noeud->getList();
        if (count($elements) == 0) {
            $x = new token_traite(new Token());
            $this->affiche($x, $niveau + 1);
            return;
        } else {
            $labels = array();
            foreach($elements as $id => &$e) {
                if (is_null($e)) {
                    //rien
                } else {
                    $this->affiche($e, $niveau + 1);
                }
            }
        }

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_block($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        if ($noeud->checkCode('{')) {
            $noeud->setCode('');
        }

        $elements = $noeud->getList();
        foreach($elements as $id => &$e) {
            $this->affiche($e, $niveau + 1);
        }
        $noeud->myGauche = $this->getIntervalleId();
        $retour = $this->saveNoeud($noeud);
        return $retour;
    }

    function affiche__break($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getNiveaux(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__case($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        if (!is_null($m = $noeud->getComparant())) {
            $this->affiche($m, $niveau + 1);
        }
        $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_cast($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getExpression(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__catch($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getException(), $niveau + 1);
        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__continue($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getNiveaux(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }
    
    function affiche_cdtternaire($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getVraie(), $niveau + 1);
        $this->affiche($noeud->getFaux(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_codephp($noeud, $niveau) {
        //le seul autorisé
        if (!isset($noeud->dotId)) {
            $noeud->dotId = $this->getNextId();
        }
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getphp_code(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__class($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');
        $classe_precedent = $this->class;
        $this->class = $noeud->getNom()->getCode();

        $tags = array();
        $abstract = $noeud->getAbstract();
        if(!is_null($abstract)) {
            $tags['abstract'][] = $this->affiche($abstract, $niveau + 1);            
        }

        $tags['name'][] = $this->affiche($noeud->getNom(), $niveau + 1);            

        $extends = $noeud->getExtends();
        if (!is_null($extends)) {
            $tags['extends'][] = $this->affiche($extends, $niveau + 1);            
        }

        $implements = $noeud->getImplements();
        if (count($implements) > 0) {
            foreach($implements as $implement) {
                $tags['implements'][] =  $this->affiche($implement, $niveau + 1);            
            }
        }

        $tags['block'][] = $this->affiche($noeud->getBlock(), $niveau + 1);            

        $noeud->myGauche = $this->getIntervalleId();
        $this->class = $classe_precedent;
        $this->tags = $tags;
        return $this->saveNoeud($noeud);
    }

    function affiche__clone($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getExpression(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_clevaleur($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getCle(), $niveau + 1);
        $this->affiche($noeud->getValeur(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_comparaison($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_concatenation($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $elements = $noeud->getList();
        $labels = array();

        foreach($elements as $id => &$e) {
            $this->affiche($e, $niveau + 1);            
        }

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_constante($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

   function affiche_decalage($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }
    
    function affiche__default($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__for($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        if (!is_null($f = $noeud->getInit())) {
            $this->affiche($f, $niveau + 1);
        }
        if (!is_null($f = $noeud->getFin())) {
            $this->affiche($f, $niveau + 1);
        }
        if (!is_null($f = $noeud->getIncrement())) {
            $this->affiche($f, $niveau + 1);
        }
        $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__foreach($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getTableau(), $niveau + 1);
        $this->affiche($noeud->getValue(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__function($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');
        $scope_precedent = $this->scope;
        $this->scope = $noeud->getName()->getCode();

        $tags = array();
        if (!is_null($m = $noeud->getVisibility())) {
            $tags['visibility'][] = $this->affiche($m, $niveau + 1);
        }
        if (!is_null($m = $noeud->getAbstract())) {
            $tags['abstract'][] = $this->affiche($m, $niveau + 1);
        }
        if (!is_null($m = $noeud->getStatic())) {
            $tags['static'][] = $this->affiche($m, $niveau + 1);
        }
        $tags['name'][] = $this->affiche($noeud->getName(), $niveau + 1);
        $tags['args'][] = $this->affiche($noeud->getArgs(), $niveau + 1);
        $tags['block'][] = $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        $this->scope = $scope_precedent;

        $this->tags = $tags;
        return $this->saveNoeud($noeud);
    }

    function affiche_functioncall($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $tags = array();
        $tags['fonction'][] = $this->affiche($noeud->getFunction(), $niveau + 1);
        $tags['args'][] = $this->affiche($noeud->getArgs(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->saveNoeud($noeud);
    }

    function affiche__global($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $elements = $noeud->getVariables();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);    
    }

    function affiche_ifthen($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $conditions = $noeud->getCondition();
        $thens = $noeud->getThen();
        $labels = array();

        $tags = array();
        
        foreach($conditions as $id => &$condition) {
            $condition->setCode('elseif');
            $tags['condition'][] = $this->affiche($condition, $niveau + 1);
            $tags['then'][] = $this->affiche($thens[$id], $niveau + 1);
        }
        
        $else = $noeud->getElse();
        if (!is_null($else)){
            $else->setCode('else');
            $tags['else'][] = $this->affiche($else, $niveau + 1);
        }

        $noeud->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->saveNoeud($noeud);
    }

    function affiche_inclusion($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getInclusion(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_literals($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_logique($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_method($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getObject(), $niveau + 1);
        $this->affiche($noeud->getMethod(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_method_static($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getClass(), $niveau + 1);
        $this->affiche($noeud->getMethod(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche__new($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $tags = array();
        $tags['name'][] = $this->affiche($noeud->getClasse(), $niveau + 1);
        $tags['args'][] = $this->affiche($noeud->getArgs(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        $this->tags = array();
        return $this->saveNoeud($noeud);        
    }

    
    function affiche_noscream($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getExpression(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_not($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $this->affiche($noeud->getExpression(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_opappend($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $this->affiche($noeud->getVariable(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_operation($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getOperation(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_parentheses($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $this->affiche($noeud->getContenu(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_preplusplus($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);    
    }

    function affiche_postplusplus($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);    
    }

    function affiche_property($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $this->affiche($noeud->getObject(), $niveau + 1);
        $this->affiche($noeud->getProperty(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_property_static($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $this->affiche($noeud->getClass(), $niveau + 1);
        $this->affiche($noeud->getProperty(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_rawtext($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_reference($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getExpression(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__return($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        if (!is_null($retour = $noeud->getRetour())) {
            $this->affiche($retour, $niveau + 1);
        }

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_sequence($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $elements = $noeud->getElements();
        if (count($elements) == 0) {
            // rien
        } else {
            $labels = array();
            $id = 0;
            foreach($elements as $id => &$e) {
                if (is_null($e)) {
                    die("cas de l'argument null ou inexistant dans une sequence");
                } else {
                    $this->affiche($e, $niveau + 1);
                }
            }
        }

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_signe($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getExpression(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__static($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getExpression(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__switch($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getOperande(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_tableau($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getIndex(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__try($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getBlock(), $niveau + 1);

        $elements = $noeud->getCatch();
        foreach($elements as $id => &$e) {
            $this->affiche($e, $niveau + 1);
        }
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__var($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');
        
        if (!is_null($noeud->getVisibility())) {
            $this->affiche($noeud->getVisibility(), $niveau + 1);
        }
        if (!is_null($noeud->getStatic())) {
            $this->affiche($noeud->getStatic(), $niveau + 1);
        }
        $variables = $noeud->getVariable();
        if (count($variables) > 0) {
            $inits = $noeud->getInit();
            foreach($variables as $id => $variable) {
                $this->affiche($variable, $niveau + 1);
                if (!is_null($inits[$id])) {
                    $this->affiche($inits[$id], $niveau + 1);
                }
            }
        
        }

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_variable($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__while($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__dowhile($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }
    
    function affiche_Token($noeud, $niveau) {
        print_r(xdebug_get_function_stack());        
        print "Attention, Token affiché : '$noeud'\n";
        die();
    }
}

?>