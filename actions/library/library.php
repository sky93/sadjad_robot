<?php
require_once dirname(__FILE__) . '/../../autoload.php';

if ( $data->text == $keyboard->buttons['go_back']) {

    $database->update("users", ['last_query' => null, 'last_request' => null], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->user_id,
        'text' => "منوی اصلی:",
        'reply_markup' => $keyboard->key_start()
    ]);

} elseif ( ($data->text == $keyboard->buttons['library'] || $data->text == '😎 پروفایل دانشجویی من') &&
    $constants->user('library_username') === null &&
    $constants->user('library_password') === null
) {

    $database->update("users", ['last_query' => 'library'], ['id' => $data->user_id]);
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' =>
            "برای نمایش کتاب‌های امانت گرفته شده از کتابخانه دانشگاه شما نیاز به نام کاربری و رمز عبور سیستم کتابخانه شما دارم. به طور پیشفرض نام کاربری و رمزعبور، شماره‌ی دانشجویی شما می‌باشد.(اطلاعات شما ذخیره نخواهد شد)" . "\n\n" .
            '🔺 ' . "   نام کاربری خود را وارد نمایید:",
        'reply_markup' => $keyboard->go_back()
    ];
    $telegram->sendMessage($content);

} elseif ( $data->text != $keyboard->buttons['library'] &&
    $constants->user('library_username') === null &&
    $constants->user('library_password') === null
) {

    $database->update('users', [
        'last_query' => 'library',
        'library_username' => $data->text
    ], ['id' => $data->user_id]);
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => '🔺' . "نام کاربری شما: " . "`" . $data->text . "`" . "\n\n" . "رمز عبور سیستم کتابخانه خود را وارد نمایید: (به طور پیشفرض شماره دانشجویی شما می‌باشد)",
        'reply_markup' => $keyboard->go_back()
    ];
    $telegram->sendMessage($content);

} elseif ( ($data->text == $keyboard->buttons['library'] || $data->text == '😎 پروفایل دانشجویی من') &&
    $constants->user('library_username') !== null &&
    $constants->user('library_password') === null
) {
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => '🔺' . "نام کاربری : " . "`" . $constants->user('library_username') . "`" . "\n\n" .  "رمز عبور سیستم کتابخانه خود را وارد نمایید:",
        'reply_markup' => $keyboard->go_back()
    ];
    $telegram->sendMessage($content);

} elseif ( $data->text != $keyboard->buttons['library'] &&
    $constants->user('library_username') !== null &&
    $constants->user('library_password') === null
) {

    $database->update("users", [
        'last_query' => 'library',
        'library_password' => $data->text
    ], ['id' => $data->user_id]);

    $login = [
        'username' => $constants->user('library_username'),
        'password' => $data->text
    ];

    $all = file_get_contents('https://api.sadjad.ac.ir/v1/library?' . http_build_query($login));
    $json = json_decode($all);
    
    if ( $json->meta->message == 'OK' ) {

        $out = '';
        foreach($json->data as $item) {
            $out .= '✅ نام کتاب: ' . $item->title . "\n";
            $out .= '👤  نویسنده:  ' . $item->author . "\n";
            $out .= '🔻  تاریخ دریافت کتاب:  ' . $item->borrow_date->persian_date_formatted . "\n";
            $out .= '🔺  مهلت تحویل کتاب:  ' . $item->borrow_date_ends->persian_date_formatted . "\n";
            $out .= '🚀  تعداد دفعات مجاز تمدید باقی مانده:  ' . $item->times_of_borrow . ' بار' . "\n\n";
        }
        $content = [
            'chat_id' => $data->chat_id,
            'parse_mode' => 'Markdown',
            'text' => $out . "\n\n" . 'آیا می‌خواهید برای استفاده های بعدی رمز شما ذخیره شود؟ (این رمز تنها توسط ربات قابل دسترس خواهد بود)',
            'reply_markup' => $keyboard->save_dont_save()
        ];
        $telegram->sendMessage($content);

    } else {

        $database->update("users", [
            'last_query' => 'library',
            'library_username' => null,
            'library_password' => null,
        ], ['id' => $data->user_id]);
        $content = [
            'chat_id' => $data->chat_id,
            'parse_mode' => 'Markdown',
            'text' => "نام کاربری یا رمز عبور شما صحیح نیست. لطفا دوباره امتحان کنید." . "\n\n" . '🔺 ' . "نام کابری خود را وارد نمایید:",
            'reply_markup' => $keyboard->go_back()
        ];
        $telegram->sendMessage($content);
    }

} elseif ( $data->text == $keyboard->buttons['save'] ) {

    $database->update("users", [
        'last_query' => null,
        'last_request' => null
    ], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => 'اطلاعات سیستم کتابخانه شما ذخیره شد. در دفعات بعدی نیازی به وارد کردن اطلاعات حساب خود ندارید.',
        'reply_markup' => $keyboard->key_start()
    ]);

} elseif ( $data->text == $keyboard->buttons['dont_save'] ) {


    $database->update("users", [
        'last_query' => null,
        'last_request' => null,
        'library_username' => null,
        'library_password' => null,
    ], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => 'اطلاعات سیستم کتابخانه شما در سیستم ذخیره نخواهد شد.',
        'reply_markup' => $keyboard->key_start()
    ]);

} elseif ( $data->text == $keyboard->buttons['library'] &&
    $constants->user('library_username') !== null &&
    $constants->user('library_password') !== null
) {

    $login = [
        'username' => $constants->user('library_username'),
        'password' => $constants->user('library_password')
    ];
      
    $all = file_get_contents('https://api.sadjad.ac.ir/v1/library?' . http_build_query($login));
    $json = json_decode($all);

    if ( $json->meta->message == 'OK' ) {

        $out = '';
        foreach($json->data as $item) {
            $out .=  '✅ نام کتاب: ' . $item->title . "\n";
            $out .=  '👤  نویسنده:  ' . $item->author . "\n";
            $out .=  '🔻  تاریخ دریافت کتاب:  ' . $item->borrow_date->persian_date_formatted . "\n";
            $out .=  '🔺  مهلت تحویل کتاب:  ' . $item->borrow_date_ends->persian_date_formatted . "\n";
            $out .=  '🚀  تعداد دفعات مجاز تمدید باقی مانده:  ' . $item->times_of_borrow . ' بار' . "\n\n";
        }
        $content = [
            'chat_id' => $data->chat_id,
            'parse_mode' => 'Markdown',
            'text' => $out,
            'reply_markup' => $keyboard->key_start()
        ];
        $telegram->sendMessage($content);
    } else {
        $database->update("users", [
            'last_query' => 'library',
            'library_username' => null,
            'library_password' => null,
        ], ['id' => $data->user_id]);
        $content = [
            'chat_id' => $data->chat_id,
            'parse_mode' => 'Markdown',
            'text' => "نام کاربری یا رمز عبور شما صحیح نیست. لطفا دوباره امتحان کنید." . "\n\n" . '🔺 ' . "نام کاربری خود را وارد نمایید:",
            'reply_markup' => $keyboard->go_back()
        ];
        $telegram->sendMessage($content);
    }

}