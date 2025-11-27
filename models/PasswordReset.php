<?php
require_once __DIR__ . '/Db.php';

class PasswordReset {
    public static function ensureTable(){
        $db = DB::get();
        $db->query("CREATE TABLE IF NOT EXISTS password_resets (
          id INT AUTO_INCREMENT PRIMARY KEY,
          user_id INT NOT NULL,
          token VARCHAR(128) NOT NULL,
          expires_at DATETIME NOT NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          INDEX(token(64)),
          FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )");
    }

    public static function create($user_id, $token, $expires_at){
        $db = DB::get();
        self::ensureTable();
        // remove previous
        $stmt = $db->prepare('DELETE FROM password_resets WHERE user_id = ?');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt = $db->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?,?,?)');
        $stmt->bind_param('iss', $user_id, $token, $expires_at);
        $ok = $stmt->execute();
        return $ok;
    }

    public static function findByToken($token){
        $db = DB::get();
        self::ensureTable();
        $stmt = $db->prepare('SELECT * FROM password_resets WHERE token = ? LIMIT 1');
        $stmt->bind_param('s', $token);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function deleteByToken($token){
        $db = DB::get();
        $stmt = $db->prepare('DELETE FROM password_resets WHERE token = ?');
        $stmt->bind_param('s', $token);
        return $stmt->execute();
    }
}
