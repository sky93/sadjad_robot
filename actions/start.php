<?php
require_once dirname(__FILE__) . '/../autoload.php';

$start = 'سلام' . ' ' . $data->first_name . ' ' . '😊' . "\n" . 'من ربات تلگرام دانشگاه صنعتی سجاد هستم و می‌خوام یک سری کارها رو برات راحت‌تر بکنم.';
$content = [
    'chat_id' => $data->chat_id,
    'text' => $start ,
    'reply_to_message_id' => $data->message_id,
    'reply_markup' => $keyboard->key_start()
];
$telegram->sendMessage($content);
