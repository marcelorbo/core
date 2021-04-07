<?php

namespace Estartar\Core;

use \PDO;
use \PDOException;

class Database {
    
    private static $instance;
    private static $error;

    public static function getInstance(): ?PDO
    {
        if (empty(self::$instance)) {
            try {
                self::$instance = new PDO(
                    CONFIG["DBDRIVER"] . ":host=" . CONFIG["DBHOST"] . ";dbname=" . CONFIG["DBNAME"] . ";port=" . CONFIG["DBPORT"],
                    CONFIG["DBUSER"],
                    CONFIG["DBPASSWORD"],
                    CONFIG["DBOPTIONS"]
                );
            } catch (PDOException $exception) {
                self::$error = $exception;
            }
        }
        return self::$instance;
    }

}