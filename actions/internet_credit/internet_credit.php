<?php
require_once dirname(__FILE__) . '/../../autoload.php';

function formatBytes($bytes, $precision = 2, $dec_point = '.', $thousands_sep = ',')
{
    $negative = $bytes < 0;
    if ($negative) $bytes *= -1;
    $size = $bytes;
    $units = ['بایت', 'کیلوبایت', 'مگابیات', 'گیگابایت', 'ترابایت', 'پتابایت', 'اتابایت', 'زتابایت', 'یکتابایت'];
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    $sz = $size / pow(1024, $power);
    if ($sz - round($sz) == 0) $precision = 0;
    if ($negative) $sz *= -1;
    return number_format($sz, $precision, $dec_point, $thousands_sep) . ' ' . $units[$power];
}

if ( $data->text == $keyboard->buttons['go_back' ]) {

    $database->update("users", ['last_query' => null], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->user_id,
        'text' => "منوی اصلی:",
        'reply_markup' => $keyboard->key_start()
    ]);

} elseif ( $data->text == $keyboard->buttons['internet'] &&
    $constants->user('internet_username') === null &&
    $constants->user('internet_password') === null
) {

    $database->update("users", ['last_query' => 'internet_credit'], ['id' => $data->user_id]);
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => "برای دریافت باقیمانده حساب اینترنت شما نیاز به نام‌کاربری و رمز عبور اینترنت شما دارم. (اطلاعات شما ذخیره نخواهد شد)" . "\n\n" . '🔺 ' . "نام کاربری حساب اینترنت خود را وارد نمایید:",
        'reply_markup' => $keyboard->go_back()
    ];
    $telegram->sendMessage($content);

} elseif ( $data->text != $keyboard->buttons['internet'] &&
    $constants->user('internet_username') === null &&
    $constants->user('internet_password') === null
) {
    $database->update("users", [
        'last_query' => 'internet_credit',
        'internet_username' => $data->text
    ], ['id' => $data->user_id]);
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => '🔺' . "نام کاربری: " . "`" . $data->text . "`" . "\n\n" . "رمز عبور حساب اینترنت خود را وارد نمایید:",
        'reply_markup' => $keyboard->go_back()
    ];
    $telegram->sendMessage($content);
} elseif ( $data->text == $keyboard->buttons['internet'] &&
    $constants->user('internet_username') !== null &&
    $constants->user('internet_password') === null
) {
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => '🔺' . "نام کاربری: " . "`" . $constants->user('internet_username') . "`" . "\n\n" .  "رمز عبور حساب اینترنت خود را وارد نمایید:",
        'reply_markup' => $keyboard->go_back()
    ];
    $telegram->sendMessage($content);
}
elseif ( $data->text != $keyboard->buttons['internet'] &&
    $constants->user('internet_username') !== null &&
    $constants->user('internet_password') === null
) {
    $telegram->sendChatAction([
        'chat_id' => $data->chat_id,
        'action' => 'typing'
    ]);

    $database->update("users", [
        'last_query' => 'internet_credit',
        'internet_password' => $data->text
    ], ['id' => $data->user_id]);

    $login = [
        'username' => $constants->user('internet_username'),
        'password' => $data->text
    ];

    $all = file_get_contents('https://sephr.me/v1/internet_credit?' . http_build_query($login));
    $json = json_decode($all);

    if ( $json->meta->message == 'OK' ) {
        $content = [
            'chat_id' => $data->chat_id,
            'parse_mode' => 'Markdown',
            'text' => 'حجم باقیمانده حساب اینترنت شما: ' . "`" .  formatBytes($json->data->remaining_credits) . "`" . "\n\n" . 'آیا می‌خواهید برای استفاده های بعدی رمز شما ذخیره شود؟ (این رمز تنها توسط ربات قابل دسترس خواهد بود)',
            'reply_markup' => $keyboard->save_dont_save()
        ];
        $telegram->sendMessage($content);
    } else {
    $telegram->sendChatAction([
        'chat_id' => $data->chat_id,
        'action' => 'typing'
    ]);
        $database->update("users", [
            'last_query' => 'internet_credit',
            'internet_username' => null,
            'internet_password' => null,
        ], ['id' => $data->user_id]);
        $content = [
            'chat_id' => $data->chat_id,
            'parse_mode' => 'Markdown',
            'text' => "نام کاربری یا رمز عبور شما صحیح نیست. لطفا دوباره امتحان کنید." . "\n\n" . '🔺 ' . "نام کاربری حساب اینترنت خود را وارد نمایید:",
            'reply_markup' => $keyboard->go_back()
        ];
        $telegram->sendMessage($content);
    }

} elseif ( $data->text == $keyboard->buttons['save'] ) {
    $database->update("users", [
        'last_query' => null
    ], ['id' => $data->user_id]);
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => 'اطلاعات حساب اینترنت شما ذخیره شد. در دفعات بعدی نیازی به وارد کردن اطلاعات حساب خود ندارید.',
        'reply_markup' => $keyboard->key_start()
    ];
    $telegram->sendMessage($content);
} elseif ( $data->text == $keyboard->buttons['dont_save'] ) {
    $database->update("users", [
        'last_query' => null,
        'internet_username' => null,
        'internet_password' => null,
    ], ['id' => $data->user_id]);
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => 'اطلاعات حساب اینترنت شما در سیستم ذخیره نخواهد شد.',
        'reply_markup' => $keyboard->key_start()
    ];
    $telegram->sendMessage($content);

} elseif ( $data->text == $keyboard->buttons['internet'] &&
    $constants->user('internet_username') !== null &&
    $constants->user('internet_password') !== null
) {
    $telegram->sendChatAction([
        'chat_id' => $data->chat_id,
        'action' => 'typing'
    ]);
    $login = [
        'username' => $constants->user('internet_username'),
        'password' => $constants->user('internet_password')
    ];

    $all = file_get_contents('https://sephr.me/v1/internet_credit?' . http_build_query($login));
    $json = json_decode($all);

    if ( $json->meta->message == 'OK' ) {
        $content = [
            'chat_id' => $data->chat_id,
            'parse_mode' => 'Markdown',
            'text' => 'حجم باقیمانده حساب اینترنت شما: ' . "`" . formatBytes($json->data->remaining_credits) . "`" ,
            'reply_markup' => $keyboard->key_start()
        ];
        $telegram->sendMessage($content);
    } else {
    $telegram->sendChatAction([
        'chat_id' => $data->chat_id,
        'action' => 'typing'
    ]);
        $database->update("users", [
            'last_query' => 'internet_credit',
            'internet_username' => null,
            'internet_password' => null,
        ], ['id' => $data->user_id]);
        $content = [
            'chat_id' => $data->chat_id,
            'parse_mode' => 'Markdown',
            'text' => "نام کاربری یا رمز عبور شما صحیح نیست. لطفا دوباره امتحان کنید." . "\n\n" . '🔺 ' . "نام کاربری حساب اینترنت خود را وارد نمایید:",
            'reply_markup' => $keyboard->go_back()
        ];
        $telegram->sendMessage($content);
    }
}
