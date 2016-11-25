<?php
require_once 'autoload.php';


switch ($data->text) {
    case '/start':
        require_once 'actions/start.php';
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
    case '➡ بازگشت به منو اصلی':
        require_once 'actions/go_back.php';
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
    default:
        echo "wrong";
}

// for log of server of texts
$file = 'telegram.txt';
$current = file_get_contents($file);
$current .= date ("Y-m-d H:i:s", time()) . ":\n" . json_encode(json_decode(file_get_contents('php://input')), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
file_put_contents($file, $current);
