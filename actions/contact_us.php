<?php
require_once dirname(__FILE__) . '/../autoload.php';

$content = [
    'chat_id' => $data->chat_id,
    'text' => "اگر پیشنهاد یا انتقادی برای بات دارید یا مشکلی در کار بات مشاهده کردید ممنون میشیم بهمون اطلاع بدین: ",
    'reply_to_message_id' => $data->message_id
];
$telegram->sendMessage($content);
