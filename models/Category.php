<?php
require_once __DIR__ . '/Db.php';

class Category {
    public static function create($name){
        $db = DB::get();
        $stmt = $db->prepare('INSERT INTO categories (name) VALUES (?)');
        $stmt->bind_param('s', $name);
        return $stmt->execute();
    }

    public static function delete($id){
        $db = DB::get();
        $stmt = $db->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public static function listAll(){
        $db = DB::get();
        $res = $db->query('SELECT * FROM categories');
        return $res->fetch_all(MYSQLI_ASSOC);
    }
}
