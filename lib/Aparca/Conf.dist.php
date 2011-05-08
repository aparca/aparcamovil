<?php
class Aparca_Conf
{
    // Database configuration
    const HOSTNAME = 'localhost';
    const USERNAME = 'username';
    const PASSWORD = 'password';
    const DBNAME   = 'dbname';
    
    // Application paramenters
    const KM = 5;
    
    static function getDsn()
    {
        return "mysql:host=". self::HOSTNAME .";dbname=" . self::DBNAME;
    }
}