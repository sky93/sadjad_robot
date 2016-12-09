<?php
require_once dirname(__FILE__) . '/../autoload.php';

$database->update("users", ['last_query' => null, 'last_request' => null], ['id' => $data->user_id]);

$telegram->sendPhoto([
    'chat_id' => $data->chat_id,
    'photo'=> "AgADBAADnLQxGzW2vAXYM47Aq0le6w9oZxkABEMDx-ggxa9zKzMBAAEC",
    'caption' => 'سلام' . ' ' . $data->first_name . ' ' . '😊' . "\n" . 'لطفا یک گزینه را از منوی زیر انتخاب نمایید:',
    'reply_markup' => $keyboard->key_start()
]);
