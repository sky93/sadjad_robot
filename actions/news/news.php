<?php
require_once dirname(__FILE__) . '/../../autoload.php';
require_once dirname(__FILE__) . '/../../lib/jdatetime.class.php';

$date = new jDateTime(true, true, 'Asia/Tehran');

if ($constants->last_message === null) {

    $database->update("users", ['last_query' => 'news'], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->chat_id,
        'text' =>  "آخرین خبرهای دانشگاه را می‌توانید از اینجا مشاهده نمایید. لطفا یک گزینه را برگزینید . . .",
        'reply_markup' => $keyboard->news()
    ]);
    
} elseif ( $data->text == $keyboard->buttons['cancel_news'] ) {

    $telegram->sendChatAction([
        'chat_id' => $data->chat_id,
        'action' => 'typing'
    ]);
    $database->update("users", ['last_query' => null], ['id' => $data->user_id]);
    $cancel_news = '';
    $rss = simplexml_load_file('http://sadjad.ac.ir/RSS.aspx?get=/mTHhwxvFPiGUClK4/Ry9w4zK6mTw826G9x7gdUuu2E=');
    foreach ( $rss->channel->item as $item ) {
        $cancel_news .= '🔺' . $item->description . " (" . '[' . 'لینک خبر' . '](' . $item->link . '))' . "\n\n";
    }
    $telegram->sendMessage([
        'chat_id' => $data->chat_id,
        'text' =>  '🔸 *' . 'اخبار لغو کلاس ها' . "*\n" . '🔸 ' . "آخرین بروزرسانی: `" . $date->date("l j F Y - H:i") . "`\n\n" . $cancel_news,
        'parse_mode' => 'Markdown',
        'reply_markup' => $keyboard->key_start()
    ]);

} elseif ( $data->text == $keyboard->buttons['acm_news'] ) {
    $telegram->sendChatAction([
        'chat_id' => $data->chat_id,
        'action' => 'typing'
    ]);
    $database->update("users", ['last_query' => null], ['id' => $data->user_id]);
    $cancel_news = '';
    $rss = simplexml_load_file('http://sadjad.ac.ir/RSS.aspx?S=19&D=0&N=8');
    foreach ( $rss->channel->item as $item ) {
        $title = $item->decsription ? $item->decsription : $item->title;
        $cancel_news .= '🔺' . $title . " (" . '[' . 'لینک خبر' . '](' . $item->link . '))' . "\n\n";
    }
    $telegram->sendMessage([
        'chat_id' => $data->chat_id,
        'text' =>  '🔸 *' . 'خبرهای مسابقه‌ی acm' . "*\n" . '🔸 ' . "آخرین بروزرسانی: `" . $date->date("l j F Y - H:i") . "`\n\n" . $cancel_news,
        'parse_mode' => 'Markdown',
        'reply_markup' => $keyboard->key_start()
    ]);

} elseif ( $data->text == $keyboard->buttons['all_news'] ) {
    $telegram->sendChatAction([
        'chat_id' => $data->chat_id,
        'action' => 'typing'
    ]);
    $database->update("users", ['last_query' => null], ['id' => $data->user_id]);
    $cancel_news = '';
    $rss = simplexml_load_file('http://sadjad.ac.ir/RSS.aspx?S=0&D=0&N=10');
    foreach ( $rss->channel->item as $item ) {
        $title = $item->decsription ? $item->decsription : $item->title;
        $cancel_news .= '🔺' . $title . " (" . '[' . 'لینک خبر' . '](' . $item->link . '))' . "\n\n";
    }
    $telegram->sendMessage([
        'chat_id' => $data->chat_id,
        'text' =>  '🔸 *' . 'آخرین خبرهای دانشگاه' . "*\n" . '🔸 ' . "آخرین بروزرسانی: `" . $date->date("l j F Y - H:i") . "`\n\n" . $cancel_news,
        'parse_mode' => 'Markdown',
        'reply_markup' => $keyboard->key_start()
    ]);

} elseif ( $data->text == $keyboard->buttons['go_back'] ) {

    $database->update("users", ['last_query' => null, 'last_request' => null], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->user_id,
        'text' => "منوی اصلی:",
        'reply_markup' => $keyboard->key_start()
    ]);
    
}