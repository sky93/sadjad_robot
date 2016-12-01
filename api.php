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


if ( $constants->last_message !== null ) {

    switch ($constants->last_message) {
        case 'contact_us':
            require_once 'actions/contact_us.php';
            break;
        case 'internet_credit':
            require_once 'actions/internet_credit.php';
            break;
        default:
            echo "wrong";
    }

} else {

    switch ($data->text) {
        case '/start':
            require_once 'actions/start.php';
            break;
        case 'ارسال به همه':
            require_once 'actions/sendtoall.php';
            break;
        case $keyboard->buttons['user_profile']:
            require_once 'actions/user_profile.php';
            break;
        case $keyboard->buttons['class_places']:
            require_once 'actions/class_places.php';
            break;
        case $keyboard->buttons['contact_us']:
            require_once 'actions/contact_us.php';
            break;
        case $keyboard->buttons['news']:
            require_once 'actions/news.php';
            break;
        case $keyboard->buttons['calender']:
            require_once 'actions/calender.php';
            break;
        case $keyboard->buttons['self']:
            require_once 'actions/self.php';
            break;
        case $keyboard->buttons['cancel_news']:
            require_once 'actions/cancel_news.php';
            break;
        case $keyboard->buttons['week']:
            require_once 'actions/week.php';
            break;
        case $keyboard->buttons['internet']:
            require_once 'actions/internet_credit.php';
            break;
        default:
            echo "wrong";
    }
}

// for log of server of texts
$file = 'telegram.txt';
$current = file_get_contents($file);
$current .= date ("Y-m-d H:i:s", time()) . ":\n" . json_encode(json_decode(file_get_contents('php://input')), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
file_put_contents($file, $current);
