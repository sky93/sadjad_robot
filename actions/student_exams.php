<?php
require_once dirname(__FILE__) . '/../autoload.php';
require_once dirname(__FILE__) . '/../lib/jdatetime.class.php';
$date = new jDateTime(true, true, 'Asia/Tehran');

if ( $data->text == $keyboard->buttons['go_back']) {

    $database->update("users", ['last_query' => null, 'last_request' => null], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->user_id,
        'text' => "منوی اصلی:",
        'reply_markup' => $keyboard->key_start()
    ]);

} elseif ( $data->text == $keyboard->buttons['student_exams'] &&
    $constants->user('stu_username') === null &&
    $constants->user('stu_password') === null
) {

    $database->update("users", ['last_query' => 'student_exams'], ['id' => $data->user_id]);
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => "برای نمایش برنامه امتحانی شما نیاز به شماره دانشجویی و رمز عبور سیستم دانشجویی شما دارم. (اطلاعات شما ذخیره نخواهد شد)" . "\n\n" . '🔺 ' . "   شماره دانشجویی خود را وارد نمایید:",
        'reply_markup' => $keyboard->go_back()
    ];
    $telegram->sendMessage($content);

} elseif ( $data->text != $keyboard->buttons['student_exams'] &&
    $constants->user('stu_username') === null &&
    $constants->user('stu_password') === null
) {
    $database->update("users", [
        'last_query' => 'student_exams',
        'stu_username' => $data->text
    ], ['id' => $data->user_id]);
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => '🔺' . "شماره دانشجویی شما: " . "`" . $data->text . "`" . "\n\n" . "رمز عبور سیستم دانشجویی خود را وارد نمایید:",
        'reply_markup' => $keyboard->go_back()
    ];
    $telegram->sendMessage($content);

} elseif ( $data->text == $keyboard->buttons['student_exams'] &&
    $constants->user('stu_username') !== null &&
    $constants->user('stu_password') === null
) {
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => '🔺' . "شماره دانشجویی: " . "`" . $constants->user('stu_username') . "`" . "\n\n" .  "رمز عبور سیستم دانشجویی خود را وارد نمایید:",
        'reply_markup' => $keyboard->go_back()
    ];
    $telegram->sendMessage($content);

} elseif ( $data->text != $keyboard->buttons['student_exams'] &&
    $constants->user('stu_username') !== null &&
    $constants->user('stu_password') === null
) {

    $database->update("users", [
        'last_query' => 'student_exams',
        'stu_password' => $data->text
    ], ['id' => $data->user_id]);

    $login = [
        'username' => $constants->user('stu_username'),
        'password' => $data->text
    ];

    $all = file_get_contents('https://sephr.me/v1/exams?' . http_build_query($login));
    $json = json_decode($all);
    
    if ( $json->meta->message == 'OK' ) {
        foreach($json->data as $item) {
            $out .=  "✅ نام درس : `" . $item->course . "`\n";
            $out .=  "👤  استاد :  `" . $item->teacher . "`\n";
            $out .=  "🕙 تاریخ برگزاری امتحان : `" . "روز " .  $item->day . "`\n\n";
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
            'last_query' => 'student_exams',
            'stu_username' => null,
            'stu_password' => null,
        ], ['id' => $data->user_id]);
        $content = [
            'chat_id' => $data->chat_id,
            'parse_mode' => 'Markdown',
            'text' => "شماره دانشجویی یا رمز عبور شما صحیح نیست. لطفا دوباره امتحان کنید." . "\n\n" . '🔺 ' . "شماره دانشجویی خود را وارد نمایید:",
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
        'text' => 'اطلاعات سیستم دانشجویی شما ذخیره شد. در دفعات بعدی نیازی به وارد کردن اطلاعات حساب خود ندارید.',
        'reply_markup' => $keyboard->key_start()
    ]);

} elseif ( $data->text == $keyboard->buttons['dont_save'] ) {


    $database->update("users", [
        'last_query' => null,
        'last_request' => null,
        'stu_username' => null,
        'stu_password' => null,
    ], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => 'اطلاعات سیستم دانشجویی شما در سیستم ذخیره نخواهد شد.',
        'reply_markup' => $keyboard->key_start()
    ]);

} elseif ( $data->text == $keyboard->buttons['student_exams'] &&
    $constants->user('stu_username') !== null &&
    $constants->user('stu_password') !== null
) {

    $login = [
        'username' => $constants->user('stu_username'),
        'password' => $constants->user('stu_password')
    ];
      
    $all = file_get_contents('https://sephr.me/v1/exams?' . http_build_query($login));
    $json = json_decode($all);

    if ( $json->meta->message == 'OK' ) {
        foreach($json->data as $item) {
            $out .=  "✅ نام درس : `" . $item->course . "`\n";
            $out .=  "👤  استاد :  `" . $item->teacher . "`\n";
            $out .=  "🕙 تاریخ برگزاری امتحان : `" . "روز " .  $item->day . "`\n\n";
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
            'last_query' => 'student_exams',
            'stu_username' => null,
            'stu_password' => null,
        ], ['id' => $data->user_id]);
        $content = [
            'chat_id' => $data->chat_id,
            'parse_mode' => 'Markdown',
            'text' => "شماره دانشجویی یا رمز عبور شما صحیح نیست. لطفا دوباره امتحان کنید." . "\n\n" . '🔺 ' . "شماره دانشجویی خود را وارد نمایید:",
            'reply_markup' => $keyboard->go_back()
        ];
        $telegram->sendMessage($content);
    }

}