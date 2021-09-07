<?php
namespace App\Service;

use PDO;

abstract class AbstractManager implements ManagerInterface
{
    protected static $pdo = null;

    protected static function getPDO(){
        if(self::$pdo === null){
            self::$pdo = new PDO(
                "mysql:host=".DB_HOST.";dbname=".DB_NAME,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //les erreurs venant de MySQL seront des Exception
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //on récupère les données de MySQL dans un tableau associatif
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                ]
            );
        }
    }

    private static function makeQuery($sql, $params = null)
    {
        self::getPDO();
        if($params){
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($params);
        }
        else $stmt = self::$pdo->query($sql);

        return $stmt;
    }

    /**
     * pour les requêtes SQL type INSERT, UPDATE & DELETE
     */
    protected function executeQuery($sql, $params)
    {
        self::getPDO();
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute($params);
    }

    protected function getResults($class, $sql, $params = null)
    {
        $stmt = self::makeQuery($sql, $params);
        $results = [];
        foreach($stmt->fetchAll() as $data){
            $results[] = new $class($data);
        }
        return $results;
    }

    protected function getOneOrNullResult($class, $sql, $params = null)
    {
        $stmt = self::makeQuery($sql, $params);
        $data = $stmt->fetch();
        
        return $data ? new $class($data) : null;
    }

    protected function getOneOrNullValue($sql, $params = null)
    {
        $stmt = self::makeQuery($sql, $params);
        //si l'affectation du résultat de fetchCOlumn à $result n'est ni faux, ni null, ni 0, ni ""
        if($result = $stmt->fetchColumn()){
           return $result; 
        }
        return null;
    }
}