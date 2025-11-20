<?php
require_once __DIR__ . '/Db.php';

class Donation {
    public static function create($campaign_id, $user_id, $amount, $reward_id = null){
        $db = DB::get();
        if($reward_id){
            $stmt = $db->prepare('INSERT INTO donations (campaign_id,user_id,amount,reward_id,payment_status) VALUES (?,?,?,?,"completed")');
            $stmt->bind_param('iidi', $campaign_id, $user_id, $amount, $reward_id);
        } else {
            $stmt = $db->prepare('INSERT INTO donations (campaign_id,user_id,amount,payment_status) VALUES (?,?,?,"completed")');
            $stmt->bind_param('iid', $campaign_id, $user_id, $amount);
        }
        $ok = $stmt->execute();
        return $ok ? $stmt->insert_id : false;
    }

    public static function createWithOptionalReward($campaign_id, $user_id, $amount, $reward_id = null){
        $db = DB::get();
        if($reward_id){
            $db->begin_transaction();
            $qstmt = $db->prepare('SELECT quantity FROM rewards WHERE id = ? FOR UPDATE');
            $qstmt->bind_param('i', $reward_id);
            $qstmt->execute();
            $qres = $qstmt->get_result();
            $rnow = $qres->fetch_assoc();
            if($rnow && !is_null($rnow['quantity']) && intval($rnow['quantity']) <= 0){
                $db->rollback();
                return false;
            }
            // insert donation
            $stmt = $db->prepare('INSERT INTO donations (campaign_id,user_id,amount,reward_id,payment_status) VALUES (?,?,?,?,"completed")');
            $stmt->bind_param('iidi', $campaign_id, $user_id, $amount, $reward_id);
            $ok = $stmt->execute();
            if(!$ok){ $db->rollback(); return false; }
            $donationId = $stmt->insert_id;
            // decrement
            $dec = $db->prepare('UPDATE rewards SET quantity = quantity - 1 WHERE id = ?');
            $dec->bind_param('i', $reward_id);
            $dec->execute();
            $db->commit();
            return $donationId;
        } else {
            return self::create($campaign_id, $user_id, $amount, null);
        }
    }

    public static function markDelivered($donation_id){
        $db = DB::get();
        $stmt = $db->prepare('UPDATE donations SET delivered = 1, delivered_at = NOW() WHERE id = ?');
        $stmt->bind_param('i', $donation_id);
        return $stmt->execute();
    }

    public static function listAll(){
        $db = DB::get();
        $res = $db->query('SELECT d.*, u.name AS donor, c.title AS campaign, r.title AS reward FROM donations d JOIN users u ON d.user_id=u.id JOIN campaigns c ON d.campaign_id=c.id LEFT JOIN rewards r ON d.reward_id=r.id ORDER BY d.created_at DESC');
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public static function listForOwner($owner_id){
        $db = DB::get();
        $stmt = $db->prepare('SELECT d.*, u.name AS donor, c.title AS campaign, r.title AS reward FROM donations d JOIN users u ON d.user_id=u.id JOIN campaigns c ON d.campaign_id=c.id LEFT JOIN rewards r ON d.reward_id=r.id WHERE c.user_id = ? ORDER BY d.created_at DESC');
        $stmt->bind_param('i', $owner_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
