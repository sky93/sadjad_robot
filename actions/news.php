<?php
require_once dirname(__FILE__) . '/../autoload.php';

$content = [
    'chat_id' => $data->chat_id,
    'text' =>  "۵ خبر آخر سایت:",
    'reply_to_message_id' => $data->message_id
];
$telegram->sendMessage($content);
$rss = simplexml_load_file('http://sadjad.ac.ir/RSS.aspx?get=gCU0oBox9yJX6b4AuhOhtD4FlyVjyie/LYVF2zIZ12WHgmuriftmUXtnayk/iNZL');

foreach ( $rss->channel->item as $item ) {
    $short = get_bitly_short_url($item->link,'amirbgh','R_236d2242f49c46daa0e5b836f0c103dd');
    $news .= '📍' .$item->title . "\n" . "منبع: " . $short."\n\n";
}
$content = [
  'chat_id' => $data->chat_id,
  'text' => $news ,
  'reply_to_message_id' => $data->message_id
];
$telegram->sendMessage($content);