<?php
require_once dirname(__FILE__) . '/../autoload.php';

if ( $constants->last_message === null ) {

    $database->update("users", [ 'last_query' => 'contact_us' ], [ 'id' => $data->user_id ]);
    $content = [
        'chat_id' => $data->chat_id,
        'text' => "اگر پیشنهاد یا انتقادی برای بات دارید و یا مشکلی در کار بات مشاهده کردید ممنون می‌شیم بهمون اطلاع بدین:",
        'reply_markup' => $keyboard->go_back()
    ];
    $telegram->sendMessage($content);

} elseif ( $constants->last_message == 'contact_us' ) {

    $database->update("users", [ 'last_query' => null ], [ 'id' => $data->user_id ]);

    if ( $data->text == $keyboard->buttons['go_back'] ) {

        $telegram->sendMessage([
            'chat_id' => $data->user_id,
            'text' => "پیامی ارسال نشد. منوی اصلی:",
            'reply_markup' => $keyboard->key_start()
        ]);

    } else {

        $database->insert("ideas", [
            "user_id" => $data->user_id,
            "message" => $data->text,
            'date_created' => date("Y-m-d H:i:s")
        ]);

        $telegram->sendMessage([
            'chat_id' => $data->user_id,
            'text' => "ممنون از شما. نظر شما ثبت گردید.",
            'reply_markup' => $keyboard->key_start()
        ]);

    }
}
