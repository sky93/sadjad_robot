<?php
require_once dirname(__FILE__) . '/../autoload.php';

function is_week_even()
{
    date_default_timezone_set('Asia/Tehran');
    $startDate = '2016-09-22';
    $endDate = date("Y/m/d");
    $date_diff = strtotime(date('Y-m',strtotime($endDate))) - strtotime(date('Y-m',strtotime($startDate)));
    $total_no_OfWeek = round(floor($date_diff/(60*60*24)) / 7) + $endDate - $startDate - 1;
    return $total_no_OfWeek % 2 ? 0 : 1;
}

if ( $data->text == $keyboard->buttons['go_back']) {

    $database->update("users", ['last_query' => null, 'last_request' => null], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->user_id,
        'text' => "منوی اصلی:",
        'reply_markup' => $keyboard->key_start()
    ]);

} elseif ( $data->text == $keyboard->buttons['student_schedule'] &&
    $constants->user('stu_username') === null &&
    $constants->user('stu_password') === null
) {

    $database->update("users", ['last_query' => 'student_schedule'], ['id' => $data->user_id]);
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => "برای نمایش برنامه درسی شما نیاز به شماره دانشجویی و رمز عبور سیستم دانشجویی شما دارم. (اطلاعات شما ذخیره نخواهد شد)" . "\n\n" . '🔺 ' . "   شماره دانشجویی خود را وارد نمایید:",
        'reply_markup' => $keyboard->go_back()
    ];
    $telegram->sendMessage($content);

} elseif ( $data->text != $keyboard->buttons['user_profile'] &&
    $constants->user('stu_username') === null &&
    $constants->user('stu_password') === null
) {
    $database->update("users", [
        'last_query' => 'student_schedule',
        'stu_username' => $data->text
    ], ['id' => $data->user_id]);
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => '🔺' . "شماره دانشجویی شما: " . "`" . $data->text . "`" . "\n\n" . "رمز عبور سیستم دانشجویی خود را وارد نمایید:",
        'reply_markup' => $keyboard->go_back()
    ];
    $telegram->sendMessage($content);

} elseif ( $data->text == $keyboard->buttons['user_profile'] &&
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

} elseif ( $data->text != $keyboard->buttons['user_profile'] &&
    $constants->user('stu_username') !== null &&
    $constants->user('stu_password') === null
) {

    $database->update("users", [
        'last_query' => 'student_schedule',
        'stu_password' => $data->text
    ], ['id' => $data->user_id]);

    $login = [
        'username' => $constants->user('stu_username'),
        'password' => $data->text
    ];

    $all = file_get_contents('https://sephr.me/v1/student_schedule?' . http_build_query($login));
    $json = json_decode($all);

    if ( $json->meta->message == 'OK' ) {
        if ((abs(date('w') + 1) % 7) == 6) {
            $out = 'امروز جمعه است! برنامه‌ی هفته‌ی بعد به شما نشان داده خواهد شد!';
            $odd_even = ! is_week_even() ? 'even' : 'odd';
            $out .= is_week_even() ? ' درضمن هفته‌ی بعد، هفته‌ی `فرد` خواهد بود.' : ' درضمن هفته‌ی بعد، هفته‌ی `زوج` خواهد بود.';
        } else {
            $out = 'برنامه‌ی این هفته‌ی شما';
            $odd_even = is_week_even() ? 'even' : 'odd';
            $out .= is_week_even() ? ' درضمن این هفته، هفته‌ی `زوج` است.' : ' درضمن این هفته، هفته‌ی `فرد` است.';
        }
        $out .= "\n\n";

        foreach ($json->data as $d) {
            $color = (abs(date('w') + 1) % 7) == $d->day_of_week ? '🔸' : '🔹';
            $out .= $color . ' ' . $d->name_of_week . ":\n";
            if (! $d->classes->$odd_even) {
                $out .= '    🔺 ' . 'هیچ کلاس ندارید!';
            } else {

                usort($d->classes->$odd_even, function($a, $b) { //Sort the array using a user defined function
                    return $a->time > $b->time ? 1 : -1; //Compare the scores
                });

                foreach ($d->classes->$odd_even as $class) {
                    $class_time = $class->time < 10 ? '0' . $class->time : $class->time ;
                    $out .= '    🔺 ' . ' `' . $class_time . ':00` — ' . $class->subject . "\n";
                }
            }
            $out .= "\n\n";

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
            'last_query' => 'student_schedule',
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

} elseif ( $data->text == $keyboard->buttons['student_schedule'] &&
    $constants->user('stu_username') !== null &&
    $constants->user('stu_password') !== null
) {

    $login = [
        'username' => $constants->user('stu_username'),
        'password' => $constants->user('stu_password')
    ];

    $all = file_get_contents('https://sephr.me/v1/student_schedule?' . http_build_query($login));
    $json = json_decode($all);

    if ( $json->meta->message == 'OK' ) {
        if ((abs(date('w') + 1) % 7) == 6) {
            $out = 'امروز جمعه است! برنامه‌ی هفته‌ی بعد به شما نشان داده خواهد شد!';
            $odd_even = ! is_week_even() ? 'even' : 'odd';
            $out .= is_week_even() ? ' درضمن هفته‌ی بعد، هفته‌ی `فرد` خواهد بود.' : ' درضمن هفته‌ی بعد، هفته‌ی `زوج` خواهد بود.';
        } else {
            $out = 'برنامه‌ی این هفته‌ی شما';
            $odd_even = is_week_even() ? 'even' : 'odd';
            $out .= is_week_even() ? ' درضمن این هفته، هفته‌ی `زوج` است.' : ' درضمن این هفته، هفته‌ی `فرد` است.';
        }
        $out .= "\n\n";

        foreach ($json->data as $d) {
            $color = (abs(date('w') + 1) % 7) == $d->day_of_week ? '🔸' : '🔹';
            $out .= $color . ' ' . $d->name_of_week . ":\n";
            if (! $d->classes->$odd_even) {
                $out .= '    🔺 ' . 'هیچ کلاس ندارید!';
            } else {

                usort($d->classes->$odd_even, function($a, $b) { //Sort the array using a user defined function
                    return $a->time > $b->time ? 1 : -1; //Compare the scores
                });


                foreach ($d->classes->$odd_even as $class) {
                    $class_time = $class->time < 10 ? '0' . $class->time : $class->time ;
                    $out .= '    🔺 ' . ' `' . $class_time . ':00` — ' . $class->subject . "\n";
                }
            }
            $out .= "\n\n";

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
            'last_query' => 'student_schedule',
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