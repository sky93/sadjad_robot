<?php
require_once dirname(__FILE__) . '/../../autoload.php';

date_default_timezone_set('Asia/Tehran');
$strtDate = '2016-09-17';
$endDate = date("Y-m-d");

$startDateWeekCnt = round(floor( date('d',strtotime($strtDate)) / 7)) ;
$endDateWeekCnt = round(ceil( date('d',strtotime($endDate)) / 7)) ;


$letter = [
    'صفرم!',
    'اوّل',
    'دوم',
    'سوم',
    'چهارم',
    'پنجم',
    'ششم',
    'هفتم',
    'هشتم',
    'نهم',
    'دهم',
    'یازدهم',
    'دوازدهم',
    'سیزدهم',
    'چهاردهم',
    'پانزدهم',
    'شانزدهم',
    'هفدهم'
];

$letter2 = [
    'صفر',
    'یک',
    'دو',
    'سه',
    'چهار',
    'پنج',
    'شش',
    'هفت',
    'هشت',
    'نه',
    'ده',
    'یازده',
    'دوازده',
    'سیزده',
    'چهارده',
    'پانزده',
    'شانزده',
    'هفده'
];

$date_diff = strtotime(date('Y-m',strtotime($endDate))) - strtotime(date('Y-m',strtotime($strtDate)));
$total_no_OfWeek = round(floor($date_diff/(60*60*24)) / 7)  + $endDateWeekCnt - $startDateWeekCnt;
$t = 16 - $total_no_OfWeek;
if( $total_no_OfWeek % 2 == 0 ) {
    $total_no_OfWeek = $letter[(int)$total_no_OfWeek];
    $t = $letter2[$t];
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' =>
            "هفته `زوج` آموزشی (هفته *$total_no_OfWeek*)" . "\n" .
            "*" . $t . "*" . ' ' . "هفته تا پایان این ترم"
    ];
    $telegram->sendMessage($content);
} else {
    $total_no_OfWeek = $letter[(int)$total_no_OfWeek];
    $t = $letter2[$t];
    $content = [
        'chat_id' => $data->chat_id,
        'parse_mode' => 'Markdown',
        'text' =>
            "هفته `فرد` آموزشی (هفته *$total_no_OfWeek*)" . "\n" .
            "*" . $t . "*" . ' ' . "هفته تا پایان این ترم"
    ];
    $telegram->sendMessage($content);
}
