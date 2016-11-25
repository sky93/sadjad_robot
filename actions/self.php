<?php
require_once dirname(__FILE__) . '/../autoload.php';


// todo:  سش نیست الان . یادم باشه اینو درست کنم 5 شنبه
$content = [
    'chat_id' => $data->chat_id,
    'text' =>
        "شنبه: قيمه" . "\n" .
        "یکشنبه : تعطیل" . "\n" .
        "دوشنبه: باقلا پلو با گوشت" . "\n" .
        "سه شنبه: چلومرغ" . "\n" .
        "چهارشنبه: چلوخورشت کرفس" . "\n" .
        "پنج شنبه: عدس پلو با گوشت",
    'reply_to_message_id' => $data->message_id
];
$telegram->sendMessage($content);
