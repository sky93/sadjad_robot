<?php
// This file is being called only when the user is not logged into the stu system.
// So we're sure either their stu_username or stu_password is null.
// We also sure that last_query is my_profile and last_request is not null too.

require_once dirname(__FILE__) . '../../autoload.php';

// We're sure this is not either username or password. So we're gonna ask the user to enter their credential information
if (
    ! isset($this_is_username_or_password) ||
    (
        isset($this_is_username_or_password) &&
        $this_is_username_or_password == false
    )
) {

    // To make things easier, we delete user's username and password. We'll ask them later.
    $database->update('users', ['stu_username' => null, 'stu_password' => null], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' =>
            'برای دریافت اطلاعات، نیاز به نام کاربری و رمز عبور حساب دانشجویی شما (stu.sadjad.ac.ir) دارم. (اطلاعات شما ذخیره نخواهد شد)' . "\n\n" .
            '🔺 ' . "نام کاربری خود را وارد نمایید:",
        'reply_markup' => $keyboard->go_back()
    ]);

// This is user's username OR their password. But we have no idea what is it! We'll check database to make sure what
// it is. username or password.
} elseif ( isset($this_is_username_or_password) && $this_is_username_or_password == true ) {

    // So the user entered their username.
    if ( $constants->user('stu_username') === null ) {
        // To make things easier, we delete user's password. We'll ask them later.
        $database->update('users', [
            'stu_username' => $data->text,
            'stu_password' => null
        ], ['id' => $data->user_id]);

        $telegram->sendMessage([
            'chat_id' => $data->chat_id,
            'parse_mode' => 'Markdown',
            'text' => '🔺' . "نام کاربری: " . "`" . $data->text . "`" . "\n\n" . "رمز عبور سیستم دانشجویی خود را وارد نمایید:",
            'reply_markup' => $keyboard->go_back()
        ]);

    // Now we're sure it's user's password. We'll update database and we're done here in this module!
    } else {

        $database->update('users', [
            'stu_password' => $data->text
        ], ['id' => $data->user_id]);

        // To prevent unnecessary sql query calls we're gonna pass user's credential information right from here.
        // we're gonna use this login variable in sub-menu files.
        $login = [
            'username' => $constants->user('stu_username'),
            'password' => $data->text
        ];

        // We also just stored username and password. Now we also need to ask user if they want to save this credentials
        // or not but we cannot make this through using this login module so we're gonna ask the next module to ask it
        // for us.
        $ask_user_to_save_credentials = true;

        switch ($constants->user('last_request')) {
            case $keyboard->buttons['student_schedule']:
                require_once dirname(__FILE__) . 'sub-menu/schedule.php';
                break;
            case $keyboard->buttons['student_exams']:
                require_once dirname(__FILE__) . 'sub-menu/exams.php';
                break;
            default:                                                          // We have no idea what user entered. So we're
                require_once dirname(__FILE__) . '../../actions/start.php';   // gonna show them the start menu.
                break;
        }

    }
}