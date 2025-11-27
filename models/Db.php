<?php
class DB {
    private static $db = null;

    public static function get(){
        if(self::$db === null){
            require_once __DIR__ . '/../db.php';
            // db.php defines `$db` in the global scope. If it was already included
            // earlier (e.g. at top of a page), `require_once` will not re-run the file
            // so `$db` won't exist in this local scope. Check both local and global.
            if (isset($db)) {
                self::$db = $db;
            } elseif (isset($GLOBALS['db'])) {
                self::$db = $GLOBALS['db'];
            } else {
                throw new Exception('Database connection not available');
            }
        }
        return self::$db;
    }
}
