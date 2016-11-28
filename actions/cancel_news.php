<?php
require_once dirname(__FILE__) . '/../autoload.php';
$content = [
    'chat_id' => $data->chat_id,
    'text' =>  "اخبار لغو کلاس ها(ممکن است کمی تاخیر داشته باشد)",
    'reply_to_message_id' => $data->message_id
];
$telegram->sendMessage($content);
//todo: ba rss dobare benvisam !
$rss = simplexml_load_file('http://sadjad.ac.ir/RSS.aspx?get=/mTHhwxvFPiGUClK4/Ry9w4zK6mTw826G9x7gdUuu2E=');
foreach ( $rss->channel->item as $item ) {
    $short = get_bitly_short_url($item->link,'amirbgh','R_236d2242f49c46daa0e5b836f0c103dd');
    $cancel_news .= '📍' . $item->description . "\n" . "منبع: " . $short."\n\n";
}
$content = [
  'chat_id' => $data->chat_id,
  'text' =>  $cancel_news,
  'reply_to_message_id' => $data->message_id
];
$telegram->sendMessage($content);