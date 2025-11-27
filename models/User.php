<?php
require_once __DIR__ . '/Db.php';

class User {
    public static function create($name, $email, $password, $role = 'inversionista'){
        $db = DB::get();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare('INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)');
        $stmt->bind_param('ssss', $name, $email, $hash, $role);
        $ok = $stmt->execute();
        return $ok ? $stmt->insert_id : false;
    }

    public static function delete($id){
        $db = DB::get();
        $stmt = $db->prepare('DELETE FROM users WHERE id = ?');
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public static function listAll(){
        $db = DB::get();
        $res = $db->query('SELECT id,name,email,role,created_at FROM users');
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public static function findByEmail($email){
        $db = DB::get();
        $stmt = $db->prepare('SELECT id,name,email,password,role FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function updatePassword($user_id, $newPassword){
        $db = DB::get();
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->bind_param('si', $hash, $user_id);
        return $stmt->execute();
    }
}
