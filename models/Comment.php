<?php
require_once __DIR__ . '/Db.php';

class Comment {
    public static function ensureTable(){
        $db = DB::get();
        $db->query("CREATE TABLE IF NOT EXISTS comments (
          id INT AUTO_INCREMENT PRIMARY KEY,
          campaign_id INT NOT NULL,
          user_id INT NOT NULL,
          content TEXT NOT NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
          FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )");
    }

    public static function create($campaign_id, $user_id, $content){
        $db = DB::get();
        self::ensureTable();
        $stmt = $db->prepare('INSERT INTO comments (campaign_id, user_id, content) VALUES (?,?,?)');
        $stmt->bind_param('iis', $campaign_id, $user_id, $content);
        $ok = $stmt->execute();
        return $ok ? $stmt->insert_id : false;
    }

    public static function listByCampaign($campaign_id){
        $db = DB::get();
        self::ensureTable();
        $stmt = $db->prepare('SELECT c.*, u.name as author FROM comments c JOIN users u ON c.user_id = u.id WHERE campaign_id = ? ORDER BY created_at DESC');
        $stmt->bind_param('i', $campaign_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
