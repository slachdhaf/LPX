<?php
// Display all errors
ini_set('display_errors',1); 
error_reporting(E_ALL);  

session_start();

// Connexion à la base de données
$m = new MongoClient();

// sélection d'une base de données
$db = $m->LPX;

// Loader automatique de classes, à inclure dans tous les PHP
function loadClass($class){
    require_once $class . '.class.php';
}
spl_autoload_register('loadClass');

?>