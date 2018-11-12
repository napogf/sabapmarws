<?php
$host = "mysql";
$user = "scapold";                   //username per la connessione al DB oracle
$pwd = "scapold";                    //password per la connessione al DB oracle
$fontedati ="sabapmarws";               //database da usare

/*************** Parametri per la creazione della tabella *************/
$tablename = "sys_users";        //nome della tabella dove saranno memorizzati gli utenti
$campousername = "user_name";  //nome del campo dove sara' memorizzato il nome utente nella tabella
$campopassword = "password";  //nome del campo dove sara' memorizzata la password dell'utente nella tabella
/**********************************************************************/
// Definisci le costanti di utilizzo comune per le cartelle


function ifdefined($name, $value) {
	if ( !defined($name) ) {
		define($name, $value);
	}
}


if(!function_exists('r')){
	function r($var,$exit=true) {
	    $calledFrom = debug_backtrace();
	    if (php_sapi_name() == 'cli') {
	        echo $calledFrom[0]['file'] . ' (line ' . $calledFrom[0]['line'] . ')'.PHP_EOL;
	        var_dump($var);
	        echo PHP_EOL;
	    } else {
            echo '<strong>' . $calledFrom[0]['file'] . '</strong> (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
            print_r($var);
	    }
	    if ($exit) {
	    	exit;
	    }
	}
}
if(!function_exists('sanitizePath')){
    function sanitizePath($string){
        // Remove special accented characters - ie. sí.
        $clean_name = strtr($string, array('Š' => 'S','Ž' => 'Z','š' => 's','ž' => 'z','Ÿ' => 'Y','À' => 'A','Á' => 'A','Â' => 'A','Ã' => 'A','Ä' => 'A','Å' => 'A','Ç' => 'C','È' => 'E','É' => 'E','Ê' => 'E','Ë' => 'E','Ì' => 'I','Í' => 'I','Î' => 'I','Ï' => 'I','Ñ' => 'N','Ò' => 'O','Ó' => 'O','Ô' => 'O','Õ' => 'O','Ö' => 'O','Ø' => 'O','Ù' => 'U','Ú' => 'U','Û' => 'U','Ü' => 'U','Ý' => 'Y','à' => 'a','á' => 'a','â' => 'a','ã' => 'a','ä' => 'a','å' => 'a','ç' => 'c','è' => 'e','é' => 'e','ê' => 'e','ë' => 'e','ì' => 'i','í' => 'i','î' => 'i','ï' => 'i','ñ' => 'n','ò' => 'o','ó' => 'o','ô' => 'o','õ' => 'o','ö' => 'o','ø' => 'o','ù' => 'u','ú' => 'u','û' => 'u','ü' => 'u','ý' => 'y','ÿ' => 'y'));
        $clean_name = strtr($clean_name, array('Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh', 'ß' => 'ss', 'Œ' => 'OE', 'œ' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u'));

        $clean_name = preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $clean_name);

        return strtolower($clean_name);
    }
}


ifdefined('DEBUG',                 false);
ifdefined('ROOT_PATH',                  dirname(dirname(__FILE__)));
ifdefined('FILES_PATH',                 ROOT_PATH . '/files');
// Definisci i parametri PHP
$phpParams = array(
        // Saràel gestore degli errori che determinerò sopprimere a video
        // gli errori in caso di produzione mandando una email, o se farli vedere in caso di debug
        'session.name'                                          => basename(ROOT_PATH),
        'session.gc_maxlifetime'                        => (3600 * 3),
);
foreach ($phpParams as $key => $val) {
        ini_set($key, $val);
}


ifdefined('PEC_PATH',			ROOT_PATH . '/pecmail');
ifdefined('LIB_PATH',                   ROOT_PATH . '/lib');
// ifdefined('PUBLIC_PATH',                dirname(ROOT_PATH) . '/public_html' . BASEURL);
ifdefined('TMP_PATH',                   ROOT_PATH . '/tmp');
ifdefined('FILES_PATH',                 ROOT_PATH . '/files');
ifdefined('CLASS_PATH',                 LIB_PATH . '/classes');
ifdefined('FUNC_PATH',                  LIB_PATH . '/functions');
ifdefined('XMLIN_PATH',                 ROOT_PATH . '/dacaricare');
ifdefined('XMLOUT_PATH',                 ROOT_PATH . '/caricati');
ifdefined('DOC_PATH',                ROOT_PATH . '/modelli');
ifdefined('INC_PATH',                ROOT_PATH . '/inc' );
ifdefined('LOG_PATH',                ROOT_PATH . '/logs' );
ifdefined('LOGIN_PATH',                ROOT_PATH . '/login' );
ifdefined('TRASPARENZA_PATH',        ROOT_PATH . '/trasparenza' );
ifdefined('ARCHIVIO_PATH',        dirname(ROOT_PATH) . '/archiviodigitale' );

ini_set('include_path', ini_get('include_path') . ':' . ROOT_PATH . '/inc');

$phpParams = array(
    // Sarà nel gestore degli errori che determinerò se sopprimere a video
    // gli errori in caso di produzione mandando una email, o se farli vedere in caso di debug
    'error_reporting'                   => E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE,
    'display_startup_errors'            => true,
    'display_errors'                    => true,
    'magic_quotes_gpc'                  => false,
    'magic_quotes_runtime'              => false,
    'magic_quotes_sybase'               => false,
    'session.auto_start'                => false,
    'session.hash_function'             => 1,           // SHA-1
    'session.hash_bits_per_character'   => 4,           // 0-9a-f
    'session.name'                      => basename(ROOT_PATH),
    'session.use_cookies'               => true,
    'session.use_only_cookies'          => true,
    'session.gc_maxlifetime'            => (3600 * 3),
	'session.save_path'            => 		ROOT_PATH . '/tmp',
    'zlib.output_compression_level'     => -1,
	'memory_limit'						=> '512M',
//     'xdebug.overload_var_dump'          => 0,
//     'xdebug.var_display_max_children'   => -1,
//     'xdebug.var_display_max_data'   => -1,
//     'xdebug.var_display_max_data'   => -1,
//     'xdebug.var_display_max_depth'  => -1,
);

foreach ($phpParams as $key => $val) {
    ini_set($key, $val);
}

// Definisci l'auto-inclusione per le classi
// Mappatura: Mia_Classe => /path/to/classes/Mia/Classe.php
function class_autoloader($className) {
	if (class_exists($className)) return;

	$filename = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
	if(!file_exists(CLASS_PATH . DIRECTORY_SEPARATOR . $filename)){
		r(CLASS_PATH . DIRECTORY_SEPARATOR . $filename,false);
		r(debug_backtrace());
	}
	require_once CLASS_PATH . DIRECTORY_SEPARATOR . $filename;
}
spl_autoload_register('class_autoloader');


// Definisci come gestire gli errori
function custom_error_handler($errno, $errstr, $errfile, $errline){
	if (error_reporting() === 0) return;

	if (error_reporting() & $errno) {
		throw new ErrorException($errstr, $errno, $errno, $errfile, $errline);
	}
}

// Tutti gli errori si trasformano in eccezioni, così gestisco tutto in un unico punto
//set_error_handler('custom_error_handler');
//set_exception_handler(array('ExceptionHandler', 'handleException'));




// Imposto la connessione a Database
$dbParams = array(
	'host'					=> 'mysql',
	'database'				=> 'sabapmarws',
	'username'				=> 'scapold',
	'password'				=> 'scapold',
	'connection_charset'	=> 'UTF8',
);


$pdo = new Db_Pdo(
	'mysql:host=' . $dbParams['host'] . ';dbname=' . $dbParams['database'],
	$dbParams['username'],
	$dbParams['password'],
	array(
		'connection_charset' => $dbParams['connection_charset'],
	)
);
// Attivo il profilatore delle query soltanto su web per debug
// $pdo->getProfiler()->setEnabled( DEBUG and php_sapi_name() <> 'cli' );

// Imposto questa come connessione globale
Db_Pdo::setInstance($pdo);
//$linkDB = @ mysqli_connect($dbParams['host'], $dbParams['username'], $dbParams['password']);

$userTable='sys_users';
$baddata= "../login.php?login=failed";

$sessionstop="login/sessionstop.php";
$dojoVersion="1.x";
$login= "login.php";
$menuType='Htabs';
$index_page= "../index.php";
$dir_upload=ROOT_PATH.'/modelli/';


//Il tempo massimo di una sessione
$sess_time_limit=time()+36000; //Attualmente è di dieci minuti
$sess_audit_login='Y';


$css_style="css/main.css";
$sess_title="Gestione Pratiche SABAP-MAR";
$top_label="";
$top_logo="immagini/logo.gif";



ifdefined('SMTPHOST', 'smtp.beniculturali.it');
ifdefined('SMTPSENDER', 'sabap-mar@beniculturali.it');


ifdefined('PEC_HOSTNAME',                strpos($_SERVER['HTTP_HOST'], 'localnet') ? 'smtp.gmail.com' :
        Db_Pdo::getInstance()->query('select valore from sys_config where chiave = "PEC_HOSTNAME"')->fetchColumn() );
ifdefined('PEC_HOSTPORT',                strpos($_SERVER['HTTP_HOST'], 'localnet') ? '465' :
Db_Pdo::getInstance()->query('select valore from sys_config where chiave = "PEC_HOSTPORT"')->fetchColumn() );
ifdefined('PEC_SMTPHOST',                strpos($_SERVER['HTTP_HOST'], 'localnet') ? 'smtp.gmail.com' :
        Db_Pdo::getInstance()->query('select valore from sys_config where chiave = "PEC_SMTPHOST"')->fetchColumn() );
ifdefined('PEC_PASSWORD',            strpos($_SERVER['HTTP_HOST'], 'localnet') ? 'lorenzO151924' :
        Db_Pdo::getInstance()->query('select valore from sys_config where chiave = "PEC_PASSWORD"')->fetchColumn() );
ifdefined('PEC_SMTPPORT',                strpos($_SERVER['HTTP_HOST'], 'localnet') ? 587 :
        Db_Pdo::getInstance()->query('select valore from sys_config where chiave = "PEC_SMTPPORT"')->fetchColumn());
ifdefined('PEC_USERNAME',                 strpos($_SERVER['HTTP_HOST'], 'localnet') ? 'giacomo.fonderico@gmail.com' :
        Db_Pdo::getInstance()->query('select valore from sys_config where chiave = "PEC_USERNAME"')->fetchColumn());
ifdefined('PEC_FROMNAME',   strpos($_SERVER['HTTP_HOST'], 'localnet') ? 'Test PEC' :
        'Soprintendenza archeologia belle arti e paesaggio delle Marche');
ifdefined('PEC_FROMNAME',   strpos($_SERVER['HTTP_HOST'], 'localnet') ? 'Test PEC' :
'Soprintendenza archeologia belle arti e paesaggio delle Marche');
