<?php
// Cette classe gère la connexion à la base de données et l'éxécution des requêtes
Class Bdd{



    /*private static $_lastConnectBdd = null;
    private static $_bddName = "logprocessorx";
    private static $_bddPassword = "LpX4764dahgfFHGEcn76XlsqsfdnCE";
    private static $_bddIp = "ly8372-001.privatesql.ha.ovh.net";

    private function __construct() {} // classe statique

    // connexion à la base de donnees
    public static function connect($nom, $mdp){
        $bdd = null;
        try{
            $bdd = new PDO('mysql:host='.self::$_bddIp.';dbname='.$nom, $nom, $mdp);
            self::$_lastConnectBdd = $bdd;
        }
        catch (Exception $e){
            die('Erreur, impossible de se connecter à la base '.$nom);
        }
        return $bdd;
    }

    // Exécute une requête sur la dernière base connectée
    public static function req($sql){
        if (is_null(self::$_lastConnectBdd)){
            die("Impossible d'utiliser la dernière BDD, aucune BDD connectée");
        }
        return self::reqBdd($sql, self::$_lastConnectBdd);
    }

    //Exécute une requête SQL
    public static function reqBdd($sql, $bdd){
        return $bdd->query($sql);
    }

    // récupère la prochaine ligne ( ou retourne false s'il n'y en a plus )
    public static function nextLine($req){
        if ($donnees = $req->fetch()){
            return $donnees;
        }else{
            $req->closeCursor();
            return false;
        }
    }

    // récupère le dernier ID de l'élément enregistré
    public static function getLastID(){
        if (is_null(self::$_lastConnectBdd)){
            die("Impossible d'utiliser la dernière BDD, aucune BDD connectée");
        }
        return self::$_lastConnectBdd->lastInsertId();
    }

    // auto connection à la base de donnée courante (i.e. *projets*)
    public static function autoConnect(){
        if (is_null(self::$_lastConnectBdd)){
            $connectedBdd = self::connect(self::$_bddName, self::$_bddPassword);
            self::req("SET NAMES 'utf8'");
            return $connectedBdd;
        }
        return self::$_lastConnectBdd;
    }*/
}
?>