<?php
class DB {
    private static $db = null;

    public static function get(){
        if(self::$db === null){
            require_once __DIR__ . '/../db.php';
            if(!isset($db)){
                throw new Exception('Database connection not available');
            }
            self::$db = $db;
        }
        return self::$db;
    }
}
