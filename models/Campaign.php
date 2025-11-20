<?php
require_once __DIR__ . '/Db.php';

class Campaign {
    public static function get($id){
        $db = DB::get();
        $stmt = $db->prepare('SELECT c.*, u.name as owner, cat.name as category FROM campaigns c JOIN users u ON c.user_id=u.id LEFT JOIN categories cat ON c.category_id=cat.id WHERE c.id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function incrementPledged($id, $amount){
        $db = DB::get();
        $stmt = $db->prepare('UPDATE campaigns SET pledged_amount = pledged_amount + ? WHERE id = ?');
        $stmt->bind_param('di', $amount, $id);
        return $stmt->execute();
    }

    public static function search($query, $category = null){
        $db = DB::get();
        $q = '%' . $query . '%';
        $sql = 'SELECT c.*, u.name as owner, cat.name as category FROM campaigns c JOIN users u ON c.user_id=u.id LEFT JOIN categories cat ON c.category_id=cat.id WHERE c.status = "approved" AND (c.title LIKE ? OR c.description LIKE ?)';
        $types = 'ss';
        $params = [$q, $q];
        if($category){
            $sql .= ' AND c.category_id = ?';
            $types .= 'i';
            $params[] = $category;
        }
        $stmt = $db->prepare($sql);
        // bind params dynamically
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }

    public static function pending(){
        $db = DB::get();
        $res = $db->query('SELECT c.*, u.name FROM campaigns c JOIN users u ON c.user_id=u.id WHERE c.status = "pending"');
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public static function setStatus($id, $status){
        $db = DB::get();
        $stmt = $db->prepare('UPDATE campaigns SET status = ? WHERE id = ?');
        $stmt->bind_param('si', $status, $id);
        return $stmt->execute();
    }

    public static function create($user_id, $title, $description, $category_id = null, $goal_amount = 0, $end_date = null){
        $db = DB::get();
        $stmt = $db->prepare('INSERT INTO campaigns (user_id,title,description,category_id,goal_amount,status,end_date) VALUES (?,?,?,?,?,"pending",?)');
        $stmt->bind_param('issids', $user_id, $title, $description, $category_id, $goal_amount, $end_date);
        $ok = $stmt->execute();
        return $ok ? $stmt->insert_id : false;
    }

    public static function update($id, $title, $description, $category_id = null, $goal_amount = 0, $end_date = null, $status = null){
        $db = DB::get();
        if($status !== null){
            $stmt = $db->prepare('UPDATE campaigns SET title=?, description=?, category_id=?, goal_amount=?, end_date=?, status=? WHERE id = ?');
            $stmt->bind_param('ssidssi', $title, $description, $category_id, $goal_amount, $end_date, $status, $id);
        } else {
            $stmt = $db->prepare('UPDATE campaigns SET title=?, description=?, category_id=?, goal_amount=?, end_date=? WHERE id = ?');
            $stmt->bind_param('ssidsi', $title, $description, $category_id, $goal_amount, $end_date, $id);
        }
        return $stmt->execute();
    }

    public static function delete($id){
        $db = DB::get();
        $stmt = $db->prepare('DELETE FROM campaigns WHERE id = ?');
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public static function listByOwner($owner_id){
        $db = DB::get();
        $stmt = $db->prepare('SELECT id,title,goal_amount,pledged_amount,end_date,status FROM campaigns WHERE user_id = ?');
        $stmt->bind_param('i', $owner_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
