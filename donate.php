<?php
require_once __DIR__ . '/models/Db.php';
require_once __DIR__ . '/models/Reward.php';
require_once __DIR__ . '/models/Donation.php';
require_once __DIR__ . '/models/Campaign.php';
require_once __DIR__ . '/models/Logger.php';
session_start();

// Helper de flash messages (almacenados en sesión)
function flash($key, $msg = null){
    if($msg === null){
        if(!empty($_SESSION[$key])){ $m = $_SESSION[$key]; unset($_SESSION[$key]); return $m; }
        return null;
    }
    $_SESSION[$key] = $msg;
}

if(empty($_SESSION['user'])){
    flash('error','Debes iniciar sesión para donar.');
    header('Location: /crowdfunding1/auth/login.php'); exit;
}
if($_SESSION['user']['role'] !== 'inversionista'){
    flash('error','Solo inversores pueden donar a campañas.');
    header('Location: /crowdfunding1/'); exit;
}
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    flash('error','Método inválido');
    header('Location: /crowdfunding1/'); exit;
}
$campaign_id = intval($_POST['campaign_id']);
$camp = Campaign::get($campaign_id);
if(!$camp){
    flash('error','Campaña no encontrada');
    header('Location: /crowdfunding1/'); exit;
}
if((int)$camp['user_id'] === (int)$_SESSION['user']['id']){
    flash('error','No puedes donar a tu propia campaña.');
    header('Location: /crowdfunding1/campaigns/view.php?id='.$campaign_id); exit;
}
$amount = floatval($_POST['amount']);
$reward_id = !empty($_POST['reward_id']) ? intval($_POST['reward_id']) : null;
if($amount <= 0){
    flash('error','Monto inválido');
    header('Location: /crowdfunding1/campaigns/view.php?id='.$campaign_id); exit;
}

// Si se seleccionó reward, validarla: pertenece a la campaña, el monto cumple el mínimo y si tiene quantity hay stock
if($reward_id){
    $reward = Reward::find($reward_id);
    if(!$reward){
        flash('error','Recompensa no encontrada');
        header('Location: /crowdfunding1/campaigns/view.php?id='.$campaign_id); exit;
    }
    if((int)$reward['campaign_id'] !== $campaign_id){
        flash('error','La recompensa no pertenece a esta campaña');
        header('Location: /crowdfunding1/campaigns/view.php?id='.$campaign_id); exit;
    }
    if($amount < floatval($reward['amount'])){
        flash('error','El monto debe ser al menos '.number_format($reward['amount'],2));
        header('Location: /crowdfunding1/campaigns/view.php?id='.$campaign_id); exit;
    }
    if(array_key_exists('quantity',$reward) && !is_null($reward['quantity']) && intval($reward['quantity']) <= 0){
        flash('error','La recompensa está agotada');
        header('Location: /crowdfunding1/campaigns/view.php?id='.$campaign_id); exit;
    }
}

// Create donation (model handles reward reservation when needed)
$donationId = Donation::createWithOptionalReward($campaign_id, $_SESSION['user']['id'], $amount, $reward_id);
if(!$donationId){ flash('error','Error al registrar la donación (recompensa agotada o error).'); header('Location: /crowdfunding1/campaigns/view.php?id='.$campaign_id); exit; }

Campaign::incrementPledged($campaign_id, $amount);
Logger::log($_SESSION['user']['id'], 'donation_create', ['donation_id'=>$donationId,'campaign_id'=>$campaign_id,'reward_id'=>$reward_id,'amount'=>$amount]);

flash('success','Donación registrada. Gracias por apoyar.');
header('Location: /crowdfunding1/campaigns/view.php?id='.$campaign_id);
exit;
?>
