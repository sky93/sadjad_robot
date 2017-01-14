<?php
require_once dirname(__FILE__) . '/../../autoload.php';

if ( $data->text == $keyboard->buttons['go_back'] ) {

    // Reset last query. So user will see the main menu.
    $database->update("users", ['last_query' => null, 'last_request' => null], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->user_id,
        'text' => "منوی اصلی:",
        'reply_markup' => $keyboard->key_start()
    ]);

} elseif ( $constants->last_message === null ) {

    // What message that user sends, it'll come to this (my_profile) module.
    $database->update("users", ['last_query' => 'my_profile', 'last_request' => null], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->user_id,
        'text' => "لطفا یک گزینه را انتخاب نمایید:",
        'reply_markup' => $keyboard->my_profile()
    ]);

} elseif (
    (
        $data->text == $keyboard->buttons['student_schedule'] ||
        $data->text == $keyboard->buttons['my_grades'] ||
        $data->text == $keyboard->buttons['my_grades_summary'] ||
        $data->text == $keyboard->buttons['student_exams'] ||           // User entered one menu from my_profile section
        $data->text == $keyboard->buttons['exam_card']                  // but they're not logged in yet.
    ) &&                                                                // So we need to log them in first.
    (
        $constants->user('stu_username') === null ||
        $constants->user('stu_password') === null
    )
) {
    // We need to know last_request. So we can call the proper action when the user entered username and their password
    $database->update("users", ['last_request' => $data->text], ['id' => $data->user_id]);
    require_once dirname(__FILE__) . '/login.php';                       // User login process

} elseif (                                                              // User entered something rather than menu,
    $constants->user('stu_username') === null ||                        // sub-menu or back button. Also either
    $constants->user('stu_password') === null                           // username or password is null (empty)
) {                                                                     // so they definitely entered either username
                                                                        // or password. So we'll redirect them to login
    $this_is_username_or_password = true;                               // section again.
    require_once dirname(__FILE__) . '/login.php';

// Finally we're sure we have both username and also password so we can safely call our sub-menu that requires
// authentication.
} elseif (
    $data->text == $keyboard->buttons['student_exams'] ||
    $data->text == $keyboard->buttons['my_grades'] ||
    $data->text == $keyboard->buttons['my_grades_summary'] ||
    $data->text == $keyboard->buttons['student_schedule'] ||
    $data->text == $keyboard->buttons['exam_card']
) {

    // We could use only one if statement but for the sake of bot's modularity we decided to put this in a separate if statement
    switch ($data->text) {
        case $keyboard->buttons['my_grades_summary']:
            require_once dirname(__FILE__) . '/sub-menu/my_grades_summary.php';
            break;
        case $keyboard->buttons['my_grades']:
            require_once dirname(__FILE__) . '/sub-menu/my_grades.php';
            break;
        case $keyboard->buttons['student_schedule']:
            require_once dirname(__FILE__) . '/sub-menu/schedule.php';
            break;
        case $keyboard->buttons['student_exams']:
            require_once dirname(__FILE__) . '/sub-menu/exams.php';
            break;
        case $keyboard->buttons['exam_card']:
            require_once dirname(__FILE__) . '/sub-menu/exams_card.php';
            break;
        default:                                                          // We have no idea what user entered. So we're
            require_once dirname(__FILE__) . '/../../actions/start.php';  // gonna show them the start menu.
            break;
    }

// If the user wants to save their username and password, we won't delete their credentials from database.
// We'll just redirect them to the start page.
} elseif ( $data->text == $keyboard->buttons['save'] ) {

    // Delete last_query and also last_request. We're done. Everything went okay.
    $database->update('users', [
        'last_query' => null,
        'last_request' => null
    ], ['id' => $data->user_id]);

    // Show user start menu
    $telegram->sendMessage([
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => 'اطلاعات سیستم دانشجویی شما ذخیره شد. در دفعات بعدی نیازی به وارد کردن اطلاعات حساب خود ندارید.',
        'reply_markup' => $keyboard->key_start()
    ]);

// We're gonna deleted the stored credentials and redirect user to the start menu.
} elseif ( $data->text == $keyboard->buttons['dont_save'] ) {

    // Delete last_query, last_request AND user credentials. We're done. Everything went okay.
    $database->update('users', [
        'last_query' => null,
        'last_request' => null,
        'stu_username' => null,
        'stu_password' => null,
    ], ['id' => $data->user_id]);

    // Show user start menu
    $telegram->sendMessage([
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' => 'اطلاعات سیستم دانشجویی شما در سیستم ذخیره نخواهد شد.',
        'reply_markup' => $keyboard->key_start()
    ]);

// Something weird happened. We're gonna show the user start menu.
} else {
    require_once dirname(__FILE__) . '/../start.php';
}
