<?php
require_once dirname(__FILE__) . '/../autoload.php';
$content = [
    'chat_id' => $data->chat_id,
    'text' => "اگر میخوای بهترین مسیر از جایی که هستی تا سالن تربیت بدنی ثامن الائمه رو پیدا کنی برای من موقعیتتو بفرست :",
    'reply_to_message_id' => $data->message_id,
    'reply_markup' => $keyboard->send_location()
];
$telegram->sendMessage($content);
$database->update("users", ['last_query' => "spo_route"], ['id' => $data->user_id]);


