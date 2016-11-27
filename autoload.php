<?php

require_once 'lib/medoo.php';
require_once 'lib/Telegram.php';
require_once 'config/config.php';
require_once 'lib/functions.php';
require_once 'lib/WebHookGet.php';
require_once 'lib/Keyboard.php';

$auth = new config();
$telegram = new Telegram($auth->bot_id);
$data = new webHookGet($telegram);
$keyboard = new keyboard();
$database = new medoo([
    'database_type' => 'mysql',
    'database_name' => $auth->database_name,
    'server' => $auth->database_host,
    'username' => $auth->database_username,
    'password' => $auth->database_password,
    'charset' => 'utf8mb4'
]);
