<?php
require_once __DIR__ . '/Db.php';

class Reward {
        public static function ensureTable(){
                $db = DB::get();
                $db->query("CREATE TABLE IF NOT EXISTS rewards (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    campaign_id INT NOT NULL,
                    title VARCHAR(255),
                    amount DECIMAL(12,2) DEFAULT 0,
                    description TEXT DEFAULT NULL,
                    quantity INT DEFAULT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
                )");
        }

    public static function find($id){
        $db = DB::get();
        $stmt = $db->prepare('SELECT * FROM rewards WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function findByCampaign($campaign_id){
        $db = DB::get();
        $stmt = $db->prepare('SELECT * FROM rewards WHERE campaign_id = ?');
        $stmt->bind_param('i', $campaign_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function create($campaign_id, $title, $amount, $description = null, $quantity = null){
        $db = DB::get();
        $stmt = $db->prepare('INSERT INTO rewards (campaign_id,title,amount,description,quantity) VALUES (?,?,?,?,?)');
        $stmt->bind_param('isdsi', $campaign_id, $title, $amount, $description, $quantity);
        $stmt->execute();
        return $stmt->insert_id;
    }

    public static function update($id, $campaign_id, $title, $amount, $description = null, $quantity = null){
        $db = DB::get();
        $stmt = $db->prepare('UPDATE rewards SET title=?,amount=?,description=?,quantity=? WHERE id = ? AND campaign_id = ?');
        $stmt->bind_param('sdiiii', $title, $amount, $description, $quantity, $id, $campaign_id);
        return $stmt->execute();
    }

    public static function delete($id, $campaign_id){
        $db = DB::get();
        $stmt = $db->prepare('DELETE FROM rewards WHERE id = ? AND campaign_id = ?');
        $stmt->bind_param('ii', $id, $campaign_id);
        return $stmt->execute();
    }
}
