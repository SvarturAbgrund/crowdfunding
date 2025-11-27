<?php
require_once __DIR__ . '/Db.php';

class Message {
    public static function ensureTable(){
        $db = DB::get();
        $db->query("CREATE TABLE IF NOT EXISTS messages (
          id INT AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(150),
          email VARCHAR(150),
          subject VARCHAR(200),
          message TEXT,
          response TEXT DEFAULT NULL,
          responded_at DATETIME DEFAULT NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public static function listAll(){
        $db = DB::get();
        self::ensureTable();
        $res = $db->query('SELECT * FROM messages ORDER BY created_at DESC');
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public static function create($name, $email, $subject, $message){
        $db = DB::get();
        self::ensureTable();
        $stmt = $db->prepare('INSERT INTO messages (name,email,subject,message) VALUES (?,?,?,?)');
        $stmt->bind_param('ssss', $name, $email, $subject, $message);
        return $stmt->execute();
    }

    public static function delete($id){
        $db = DB::get();
        $stmt = $db->prepare('DELETE FROM messages WHERE id = ?');
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public static function respond($id, $response){
        $db = DB::get();
        self::ensureTable();
        $stmt = $db->prepare('UPDATE messages SET response = ?, responded_at = NOW() WHERE id = ?');
        $stmt->bind_param('si', $response, $id);
        return $stmt->execute();
    }
}
