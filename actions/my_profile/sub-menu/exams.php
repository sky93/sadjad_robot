<?php
// In here we're completely sure that we have both username and password. So things are much easier to do.
// We're also sire that out last_query is my_profile and last_request is $constants->user('last_request) (not null)

require_once dirname(__FILE__) . '/../../../autoload.php';

/**
 *  Checks if current week is even or odd
 *
 * @return int  if current week is even it return 1 (true) otherwise it returns 0 (false)
 */
function is_week_even()
{
    date_default_timezone_set('Asia/Tehran');
    $startDate = '2016-09-17';
    $endDate = date("Y/m/d");
    $startDateWeekCnt = round(floor( date('d',strtotime($startDate)) / 7)) ;
    $endDateWeekCnt = round(floor( date('d',strtotime($endDate)) / 7)) ;
    $date_diff = strtotime(date('Y-m',strtotime($endDate))) - strtotime(date('Y-m',strtotime($startDate)));
    $total_no_OfWeek = round(floor($date_diff/(60*60*24)) / 7) + $endDateWeekCnt - $startDateWeekCnt;
    return $total_no_OfWeek % 2 ? 0 : 1;
}

// If this file is being called by login.php file, then we already have $login variable. So we don't need to get
// information from database.
if ( ! isset($login) ) {
    $login = [
        'username' => $constants->user('stu_username'),
        'password' => $constants->user('stu_password'),
    ];
}

// Sadjad university of Technology official API system
$all = file_get_contents('https://api.sadjad.ac.ir/v1/student_schedule?' . http_build_query($login));
$json = json_decode($all);

if ( $json->meta->message == 'OK' ) {

    $out = '';
    foreach($json->data as $item) {
        $out .=  '✅ نام درس : ' . '`' . $item->course . '`' . "\n";
        $out .=  '👤  استاد :  ' . '`' . $item->teacher . '`' . "\n";
        $out .=  '🕙 تاریخ برگزاری امتحان : ' . '`' . 'روز ' .  $item->day . '`' . "\n\n";
    }

    // By now we have our desired content. Now we check if bot has saved password or not.
    if ( isset($ask_user_to_save_credentials) ) {
        $content = [
            'chat_id' => $data->chat_id,
            'parse_mode' => 'Markdown',
            'text' => $out . "\n\n" . 'آیا می‌خواهید برای استفاده های بعدی رمز شما ذخیره شود؟ (این رمز تنها توسط ربات قابل دسترس خواهد بود)',
            'reply_markup' => $keyboard->save_dont_save()
        ];
    } else {
        $content = [
            'chat_id' => $data->chat_id,
            'parse_mode' => 'Markdown',
            'text' => $out,
            'reply_markup' => $keyboard->key_start()
        ];
    }
    $telegram->sendMessage($content);

    // Response is not 200 (Temporary server maintenance or invalid user credentials)
} else {

    // We'll clear user's username and password anyway.
    $database->update("users", [
        'stu_username' => null,
        'stu_password' => null,
    ], ['id' => $data->user_id]);

    $telegram->sendMessage([
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => "شماره دانشجویی یا رمز عبور شما صحیح نیست. لطفا دوباره امتحان کنید." . "\n\n" . '🔺 ' . "شماره دانشجویی خود را وارد نمایید:",
        'reply_markup' => $keyboard->go_back()
    ]);
}
