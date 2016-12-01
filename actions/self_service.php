<?php
require_once dirname(__FILE__) . '/../autoload.php';

if ( $data->text == $keyboard->buttons['go_back'] ) {

    $database->update("users", ['last_query' => null, 'last_request' => null], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->user_id,
        'text' => "منوی اصلی:",
        'reply_markup' => $keyboard->key_start()
    ]);

} elseif ( $data->text == $keyboard->buttons['self_service'] ) {

    $database->update("users", ['last_query' => 'self_service'], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->user_id,
        'text' => "لطفا یک گزینه را انتخاب نمایید:",
        'reply_markup' => $keyboard->self_service_main()
    ]);

} elseif (
    (
        $data->text == $keyboard->buttons['self_service_credit'] ||
        $data->text == $keyboard->buttons['self_service_this_week']
    ) &&
    $constants->user('self_service_username') === null &&
    $constants->user('self_service_password') === null
) {
    $database->update("users", ['last_request' => $data->text], ['id' => $data->user_id]);
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => "برای دریافت باقیمانده حساب تغذیه شما نیاز به نام‌کاربری و رمز عبور تغذیه شما دارم. (اطلاعات شما ذخیره نخواهد شد)" . "\n\n" . '🔺 ' . "نام کاربری حساب تغذیه خود را وارد نمایید:",
        'reply_markup' => $keyboard->go_back()
    ];
    $telegram->sendMessage($content);
} elseif (
    $data->text != $keyboard->buttons['self_service_credit'] &&
    $data->text != $keyboard->buttons['self_service_this_week'] &&
    $constants->user('self_service_username') === null &&
    $constants->user('self_service_password') === null
) {

    $database->update("users", [
        'last_query' => 'self_service',
        'self_service_username' => $data->text
    ], ['id' => $data->user_id]);
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => '🔺' . "نام کاربری: " . "`" . $data->text . "`" . "\n\n" . "رمز عبور حساب تغذیه خود را وارد نمایید:",
        'reply_markup' => $keyboard->go_back()
    ];
    $telegram->sendMessage($content);

} elseif (
    ($data->text == $keyboard->buttons['self_service_credit'] ||
    $data->text == $keyboard->buttons['self_service_this_week']) &&
    $constants->user('self_service_username') !== null &&
    $constants->user('self_service_password') === null
) {
    $database->update("users", ['last_request' => $data->text], ['id' => $data->user_id]);
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => '🔺' . "نام کاربری: " . "`" . $constants->user('internet_username') . "`" . "\n\n" .  "رمز عبور حساب اینترنت خود را وارد نمایید:",
        'reply_markup' => $keyboard->go_back()
    ];
    $telegram->sendMessage($content);
}
elseif (
    ($data->text != $keyboard->buttons['self_service_credit'] ||
        $data->text != $keyboard->buttons['self_service_this_week']) &&
    $constants->user('self_service_username') !== null &&
    $constants->user('self_service_password') === null
) {
    $database->update("users", [
        'last_query' => 'self_service',
        'self_service_password' => $data->text
    ], ['id' => $data->user_id]);

    $login = [
        'username' => $constants->user('self_service_username'),
        'password' => $data->text
    ];

    if ( $constants->user('last_request') == $keyboard->buttons['self_service_credit'] ) {
        $all = file_get_contents('https://sephr.me/v1/self_service_credits?' . http_build_query($login));
        $json = json_decode($all);
        if ($json->meta->message == 'OK') {
            $content = [
                'chat_id' => $data->chat_id,
                'parse_mode' => 'Markdown',
                'text' => 'حجم باقیمانده حساب تغذیه شما: ' . "`" . $json->data->remaining_credits . " ريال`" . "\n\n" . 'آیا می‌خواهید برای استفاده های بعدی رمز شما ذخیره شود؟ (این رمز تنها توسط ربات قابل دسترس خواهد بود)',
                'reply_markup' => $keyboard->save_dont_save()
            ];
            $telegram->sendMessage($content);
        } else {
            $database->update("users", [
                'last_query' => 'self_service',
                'self_service_username' => null,
                'self_service_password' => null,
                'last_request' => null
            ], ['id' => $data->user_id]);
            $content = [
                'chat_id' => $data->chat_id,
                'parse_mode' => 'Markdown',
                'text' => "نام کاربری یا رمز عبور شما صحیح نیست. لطفا دوباره امتحان کنید." . "\n\n" . '🔺 ' . "نام کاربری حساب اغذیه خود را وارد نمایید:",
                'reply_markup' => $keyboard->go_back()
            ];
            $telegram->sendMessage($content);
        }
    } else {
        $all = file_get_contents('https://sephr.me/v1/self_service_menu?' . http_build_query($login));
        $json = json_decode($all);

        if ( $json->meta->message == 'OK' ) {
            $out = '';

            foreach ($json->data as $d) {
                $color = (abs(date('w') + 1) % 7) == $d->day_of_week ? '🔸' : '🔹';
                $menu = $d->menu === null ? '(سلف تعطیل است)' : $d->menu;
                $out .= $color . ' ' . $d->name_of_week . ": `" . $menu . "`\n";
            }

            $content = [
                'chat_id' => $data->chat_id,
                'parse_mode' => 'Markdown',
                'text' => '🍳 برنامه این هفته:' . "\n\n" . $out . "\n\n" . 'آیا می‌خواهید برای استفاده های بعدی رمز شما ذخیره شود؟ (این رمز تنها توسط ربات قابل دسترس خواهد بود)',
                'reply_markup' => $keyboard->save_dont_save()
            ];
            $telegram->sendMessage($content);
        } else {
            $database->update("users", [
                'last_query' => 'self_service',
                'self_service_username' => null,
                'self_service_password' => null,
            ], ['id' => $data->user_id]);
            $content = [
                'chat_id' => $data->chat_id,
                'parse_mode' => 'Markdown',
                'text' => "نام کاربری یا رمز عبور شما صحیح نیست. لطفا دوباره امتحان کنید." . "\n\n" . '🔺 ' . "نام کاربری حساب تغذیه خود را وارد نمایید:",
                'reply_markup' => $keyboard->go_back()
            ];
            $telegram->sendMessage($content);
        }
    }

} elseif ( $data->text == $keyboard->buttons['save'] ) {
    $database->update("users", [
        'last_query' => null
    ], ['id' => $data->user_id]);
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => 'اطلاعات حساب تغذیه شما ذخیره شد. در دفعات بعدی نیازی به وارد کردن اطلاعات حساب خود ندارید.',
        'reply_markup' => $keyboard->key_start()
    ];
    $telegram->sendMessage($content);
} elseif ( $data->text == $keyboard->buttons['dont_save'] ) {
    $database->update("users", [
        'last_query' => null,
        'last_request' => null,
        'self_service_username' => null,
        'self_service_password' => null,
    ], ['id' => $data->user_id]);
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => 'اطلاعات حساب تغذیه شما در سیستم ذخیره نخواهد شد.',
        'reply_markup' => $keyboard->key_start()
    ];
    $telegram->sendMessage($content);

} elseif ( $data->text == $keyboard->buttons['self_service_credit'] &&
    $constants->user('internet_username') !== null &&
    $constants->user('internet_password') !== null
) {

    $database->update("users", [
        'last_query' => null,
        'last_request' => null,
    ], ['id' => $data->user_id]);

    $login = [
        'username' => $constants->user('self_service_username'),
        'password' => $constants->user('self_service_password')
    ];

    $all = file_get_contents('https://sephr.me/v1/self_service_credits?' . http_build_query($login));
    $json = json_decode($all);

    if ( $json->meta->message == 'OK' ) {
        $content = [
            'chat_id' => $data->chat_id,
            'parse_mode' => 'Markdown',
            'text' => 'باقیمانده حساب تغذیه شما: ' . "`" . $json->data->remaining_credits . " ريال`" ,
            'reply_markup' => $keyboard->key_start()
        ];
        $telegram->sendMessage($content);
    } else {
        $database->update("users", [
            'last_query' => 'self_service',
            'self_service_username' => null,
            'self_service_password' => null,
        ], ['id' => $data->user_id]);
        $content = [
            'chat_id' => $data->chat_id,
            'parse_mode' => 'Markdown',
            'text' => "نام کاربری یا رمز عبور شما صحیح نیست. لطفا دوباره امتحان کنید." . "\n\n" . '🔺 ' . "نام کاربری حساب تغذیه خود را وارد نمایید:",
            'reply_markup' => $keyboard->go_back()
        ];
        $telegram->sendMessage($content);
    }
} elseif ( $data->text == $keyboard->buttons['self_service_this_week'] &&
    $constants->user('internet_username') !== null &&
    $constants->user('internet_password') !== null
) {

    $database->update("users", [
        'last_query' => null
    ], ['id' => $data->user_id]);

    $login = [
        'username' => $constants->user('self_service_username'),
        'password' => $constants->user('self_service_password')
    ];

    $all = file_get_contents('https://sephr.me/v1/self_service_menu?' . http_build_query($login));
    $json = json_decode($all);

    if ( $json->meta->message == 'OK' ) {
        $out = '';

        foreach ($json->data as $d) {
            $color = (abs(date('w') + 1) % 7) == $d->day_of_week ? '🔸' : '🔹';
            $menu = $d->menu === null ? '(سلف تعطیل است)' : $d->menu;
            $out .= $color . ' ' . $d->name_of_week . ": `" . $menu . "`\n";
        }

        $content = [
            'chat_id' => $data->chat_id,
            'parse_mode' => 'Markdown',
            'text' => '🍳 برنامه این هفته:' . "\n\n" . $out ,
            'reply_markup' => $keyboard->key_start()
        ];
        $telegram->sendMessage($content);
    } else {
        $database->update("users", [
            'last_query' => 'self_service',
            'self_service_username' => null,
            'self_service_password' => null,
        ], ['id' => $data->user_id]);
        $content = [
            'chat_id' => $data->chat_id,
            'parse_mode' => 'Markdown',
            'text' => "نام کاربری یا رمز عبور شما صحیح نیست. لطفا دوباره امتحان کنید." . "\n\n" . '🔺 ' . "نام کاربری حساب تغذیه خود را وارد نمایید:",
            'reply_markup' => $keyboard->go_back()
        ];
        $telegram->sendMessage($content);
    }

}

