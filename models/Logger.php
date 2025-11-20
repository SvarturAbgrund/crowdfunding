<?php
require_once __DIR__ . '/Db.php';

class Logger {
    public static function ensureDir(){
        $dir = __DIR__ . '/../logs';
        if(!is_dir($dir)) @mkdir($dir, 0755, true);
        return $dir;
    }

    public static function log($user_id, $action, $meta = []){
        $dir = self::ensureDir();
        $file = $dir . '/activity.log';
        $entry = [
            'ts' => date('c'),
            'user_id' => $user_id,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'cli',
            'script' => $_SERVER['REQUEST_URI'] ?? ($_SERVER['PHP_SELF'] ?? ''),
            'action' => $action,
            'meta' => $meta
        ];
        @file_put_contents($file, json_encode($entry, JSON_UNESCAPED_SLASHES) . "\n", FILE_APPEND | LOCK_EX);
    }

    public static function logPageView(){
        $user_id = !empty($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : null;
        $action = 'page_view';
        $meta = ['uri' => $_SERVER['REQUEST_URI'] ?? '', 'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET'];
        self::log($user_id, $action, $meta);
    }
}
