<?php
require_once dirname(__FILE__) . '/../autoload.php';

$content = [
    'chat_id' => $data->chat_id,
    'text' => "در حال ساخت ... " ,
    'reply_to_message_id' => $data->message_id,
    'reply_markup' => $keyboard->key_start()
];
$telegram->sendMessage($content);
