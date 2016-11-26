<?php
require_once dirname(__FILE__) . '/../autoload.php';

$content = [
    'chat_id' => $data->chat_id,
    'photo'=> "AgADBAAD97wxGwMojwUs4vlLZ-gYj_FjZhkABD6j1ym4SlASiewAAgI",
    'caption'=> "* نیمسال اول96-95 *",
    'reply_to_message_id' => $data->message_id
];

$telegram->sendPhoto($content);
