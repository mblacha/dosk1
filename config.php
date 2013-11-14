<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'dosk1';
$CFG->dbuser    = 'root';
$CFG->dbpass    = 'monika';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbsocket' => 0,
);

$CFG->wwwroot   = 'http://localhost/dosk1';
$CFG->dataroot  = 'D:\\xampp\\moodledata_dosk1';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;
$CFG->opensslcnf = 'C:/xampp/apache/conf/openssl.cnf';

//--$CFG->passwordsaltmain = 'gL@T@y!VQpSn)RH0-z~g&@sV';

require_once(dirname(__FILE__) . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
ini_set('display_errors', '1');
error_reporting(E_ALL);
