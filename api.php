<?php
require_once 'autoload.php';
date_default_timezone_set('Asia/Tehran');

$database->insert("users", [
    "id" => $data->user_id,
    "username" => $data->username,
    "first_name" => $data->first_name,
    "last_name" => $data->last_name,
    'date_created' => date("Y-m-d H:i:s")
]);

//// for log of server of texts
//$file = 'telegram.txt';
//$current = file_get_contents($file);
//$current .= date ("Y-m-d H:i:s", time()) . ":\n" . json_encode(json_decode(file_get_contents('php://input')), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
//file_put_contents($file, $current);


if ($constants->last_message !== null && $data->text != '/start') {

    switch ($constants->last_message) {
        case 'news':
            require_once 'actions/news.php';
            break;
        case 'contact_us':
            require_once 'actions/contact_us.php';
            break;
        case 'student_schedule':
            require_once 'actions/student_schedule.php';
            break;
        case 'internet_credit':
            require_once 'actions/internet_credit.php';
            break;
        case 'self_service':
            require_once 'actions/self_service.php';
            break;
        case 'location':
            require_once 'actions/location.php';
            break;
        case 'send_all':
            require_once 'actions/send_to_all.php';
            break;
        default:
            require_once 'actions/start.php';
            break;
    }

} else {

    switch ($data->text) {
        case '/start':
            require_once 'actions/start.php';
            break;
        case 'ارسال به همه':
            require_once 'actions/sendtoall.php';
            break;
        case $keyboard->buttons['student_schedule']:
            require_once 'actions/student_schedule.php';
            break;
        case $keyboard->buttons['contact_us']:
            require_once 'actions/contact_us.php';
            break;
        case $keyboard->buttons['news']:
            require_once 'actions/news.php';
            break;
        case $keyboard->buttons['location']:
            require_once 'actions/location.php';
            break;
        case $keyboard->buttons['self_service']:
            require_once 'actions/self_service.php';
            break;
        case $keyboard->buttons['week']:
            require_once 'actions/week.php';
            break;
        case $keyboard->buttons['internet']:
            require_once 'actions/internet_credit.php';
            break;
        default:
            require_once 'actions/start.php';
            break;
    }
}
