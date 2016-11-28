<?php
require_once dirname(__FILE__) . '/../autoload.php';

date_default_timezone_set('Asia/Tehran');
$startDate = '2016-09-22';
$endDate = date("Y/m/d");

$startDateWeekCnt = round(floor( date('d',strtotime($startDate)) / 7)) ;
$endDateWeekCnt = round(ceil( date('d',strtotime($endDate)) / 7)) ;

$date_diff = strtotime(date('Y-m',strtotime($endDate))) - strtotime(date('Y-m',strtotime($startDate)));
$total_no_OfWeek = round(floor($date_diff/(60*60*24)) / 7) + 2 ;
$t = 16 - $total_no_OfWeek;
if( $total_no_OfWeek % 2 == 0 ) {
    $content = [
        'chat_id' => $data->chat_id,
        'text' =>
            "هفته زوج آموزشی ( هفته $total_no_OfWeek)" . "\n" .
            "$t هفته تا پایان این ترم",
        'reply_to_message_id' => $data->message_id
    ];
    $telegram->sendMessage($content);
} else {
    $content = [
        'chat_id' => $data->chat_id,
        'text' =>
            "هفته فرد آموزشی ( هفته $total_no_OfWeek)" . "\n" .
            "$t هفته تا پایان این ترم",
        'reply_to_message_id' => $data->message_id
    ];
    $telegram->sendMessage($content);
}
