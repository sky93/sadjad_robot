<?php
require_once dirname(__FILE__) . '/../autoload.php';

$database->update("users", ['last_query' => null, 'last_request' => null], ['id' => $data->user_id]);

$telegram->sendMessage([
    'chat_id' => $data->chat_id,
    'text' => 'سلام' . ' ' . $data->first_name . ' ' . '😊' . "\n" . 'من ربات تلگرام دانشگاه صنعتی سجاد هستم و می‌خوام یک سری کارها رو برات راحت‌تر بکنم.',
    'reply_to_message_id' => $data->message_id,
    'reply_markup' => $keyboard->key_start()
]);
