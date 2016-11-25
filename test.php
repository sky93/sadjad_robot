<?php
require_once 'medoo.php';
require_once 'Telegram.php';

$bot_id = "#########--D0dOID-QhD4w1Fb5jKOiyxgY";

$telegram = new Telegram($bot_id);
$result = $telegram->getData();
$chat_id = $result["message"]["chat"]["id"];
$text = $result["message"]["text"];
$first_name = $result["message"]["from"]["first_name"];
$last_name = $result["message"]["from"]["last_name"];
$message_id = $result["message"]["message_id"];


// functions
function get_string_between($string, $start, $end)
{
       $string = ' ' . $string;
       $ini = strpos($string, $start);
       if ($ini == 0) return '';
       $ini += strlen($start);
       $len = strpos($string, $end, $ini) - $ini;
       return substr($string, $ini, $len);
}
function get_bitly_short_url($url,$login,$appkey,$format='json') {
	$connectURL = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).'&format='.$format;
	$obj = json_decode(file_get_contents($connectURL), true);
    return $obj["data"]["url"];
}

// all keyboards
$link_button =
'{"inline_keyboard":[
[
{
    "text":"بیشتر بخوانید ...",
    "url":"https://sadjad.ac.ir"
    }]],
    "ForceReply":
    {
     "force_reply" : true
    }
}';
$buttons = [
    'my_uni'       => '💡 دانشکده من',

    'self'         => '🍗 سیستم تغذیه',
    'user_profile' => '👤 پروفایل دانشجویی',

    'class_places' => '👣 مکان کلاس من',
    'week'         => '⁉ ️هفته آموزشی',

    'calender'     => 'تقویم آموزشی',
    'map'          => '📍 مسیریابی تا دانشگاه',
    
    'cancel_news'  => 'اخبار لغو کلاس ها',
    'news'         => 'آخرین اخبار دانشگاه',
    
    'contact_us'   => '✍ تماس با ما'
];
$key_start =
'{
   "keyboard": [
                  [
                     "' . $buttons['my_uni'] . '"
                 ],
                 [
                    "' . $buttons['self'] . '",
                    "' . $buttons['user_profile'] . '"
                 ],
                 [
                    "' . $buttons['class_places'] . '",
                    "' . $buttons['week'] . '"
                 ],
                 [
                    "' . $buttons['calender'] . '",
                    "' . $buttons['map'] . '"
                 ],
                 [
                    "' . $buttons['cancel_news'] . '",
                    "' . $buttons['news'] . '"
                 ],
                 [
                    "' . $buttons['contact_us'] . '"
                 ]
              ],
              "resize_keyboard" : true,
              "ForceReply":{
                  "force_reply" : true
              }
}';

$key_uni =
'{
 "keyboard":[
[
    "دانشکده مهندسی کامپیوتر"
]
,
[
    "دانشکده مهندسی صنایع و مواد"
]
,
[
    "دانشکده مهندسی برق و مهندسی پزشکی"
]
,
[
    "دانشکده مهندسی عمران و معماری"
],
[
    "➡ بازگشت به منو اصلی️"
]
            ],
            "resize_keyboard" : true,
            "ForceReply":{
                "force_reply" : true
            }
        }';

$key_bargh =
'{
 "keyboard":[
[
    "همایش های Sadjad I/O"
]
,
[
    "مسابقات برنامه نویسی ACM"
]
,
[
    "مسابقات کشوری accept"
]
,
[
    "بازگشت به منو اصلی➡️"
]
            ],
            "resize_keyboard" : true,
            "ForceReply":{
                "force_reply" : true
            }
        }';

// text process


if( $text == '/start' ) {
      $start = 'سلام' . ' ' . $first_name . ' ' . '😊' . "\n" . 'من ربات تلگرام دانشگاه صنعتی سجاد هستم و می‌خوام یک سری کارها رو برات راحت‌تر بکنم.';
      $content = [
          'chat_id' => $chat_id,
          'text' => $start ,
          'reply_to_message_id' => $message_id,
          'reply_markup' => $key_start
      ];
      $telegram->sendMessage($content);

} elseif ( $text== '👤 پروفایل دانشجویی' ) {
      $content = [
          'chat_id' => $chat_id,
          'text' => "در حال ساخت ... " ,
          'reply_to_message_id' => $message_id,
          'reply_markup' => $key_start
      ];
      $telegram->sendMessage($content);
}
if( $text == '👣 مکان کلاس من' ) {
      $content = [
          'chat_id' => $chat_id,
          'text' => "در دست کد نویسی ... ",
          'reply_to_message_id' => $message_id,
          'reply_markup' => $key_start
      ];
      $telegram->sendMessage($content);
} elseif( $text == '✍ تماس با ما' ) {
      $content = [
          'chat_id' => $chat_id,
          'text' => "اگر پیشنهاد یا انتقادی برای بات دارید یا مشکلی در کار بات مشاهده کردید ممنون میشیم بهمون اطلاع بدین: ",
          'reply_to_message_id' => $message_id
      ];
      $telegram->sendMessage($content);
} elseif($text == '➡ بازگشت به منو اصلی' ) {
      $content = [
          'chat_id' => $chat_id,
          'text' => "منوی دانشکده ها: ",
          'reply_to_message_id' => $message_id,
          'reply_markup' => $key_start
      ];
      $telegram->sendMessage($content);
} elseif( $text == 'آخرین اخبار دانشگاه' ) {
    $content = [
        'chat_id' => $chat_id,
        'text' =>  "۵ خبر آخر سایت:",
        'reply_to_message_id' => $message_id
    ];
    $telegram->sendMessage($content);  
    $rss = simplexml_load_file('http://sadjad.ac.ir/RSS.aspx?get=gCU0oBox9yJX6b4AuhOhtD4FlyVjyie/LYVF2zIZ12WHgmuriftmUXtnayk/iNZL');
    
    foreach ( $rss->channel->item as $item ) {
      $short = get_bitly_short_url($item->link,'amirbgh','#################');
      $content = [
          'chat_id' => $chat_id,
          'text' =>  $item->title . "\n\n" . "منبع: " . $short,
          'reply_to_message_id' => $message_id
      ];
      $telegram->sendMessage($content); 
    }
} elseif( $text == 'تقویم آموزشی' ) {
      $content = [
          'chat_id' => $chat_id,
          'photo'=> "AgADBAAD97wxGwMojwUs4vlLZ-gYj_FjZhkABD6j1ym4SlASiewAAgI",
          'caption'=> "* نیمسال اول96-95 *",
          'reply_to_message_id' => $message_id
      ];
      $telegram->sendPhoto($content);
} elseif($text == '🍗 سیستم تغذیه') {
      // todo:  سش نیست الان . یادم باشه اینو درست کنم 5 شنبه
      $content = [
          'chat_id' => $chat_id,
          'text' =>
              "شنبه: قيمه" . "\n" .
              "یکشنبه : تعطیل" . "\n" .
              "دوشنبه: باقلا پلو با گوشت" . "\n" .
              "سه شنبه: چلومرغ" . "\n" .
              "چهارشنبه: چلوخورشت کرفس" . "\n" .
              "پنج شنبه: عدس پلو با گوشت",
          'reply_to_message_id' => $message_id
      ];
      $telegram->sendMessage($content);
} elseif( $text == 'اخبار لغو کلاس ها' ) {
    //todo: ba rss dobare benvisam !
    $rss = simplexml_load_file('http://sadjad.ac.ir/RSS.aspx?get=/mTHhwxvFPiGUClK4/Ry9w4zK6mTw826G9x7gdUuu2E=');
    foreach ($rss->channel->item as $item) {
      $short = get_bitly_short_url($item->link,'amirbgh','R_236d2242f49c46daa0e5b836f0c103dd');
      $content = [
          'chat_id' => $chat_id,
          'text' =>  '📍' . $item->description . "\n" . "منبع: " . $short,
          'reply_to_message_id' => $message_id
      ];
      $telegram->sendMessage($content); 
    }
} elseif ( $text == '⁉هفته آموزشی' ) {
      $startDate = '2016-09-22';
      $endDate = date("Y/m/d");

      $startDateWeekCnt = round(floor( date('d',strtotime($startDate)) / 7)) ;
      $endDateWeekCnt = round(ceil( date('d',strtotime($endDate)) / 7)) ;

      $date_diff = strtotime(date('Y-m',strtotime($endDate))) - strtotime(date('Y-m',strtotime($startDate)));
      $total_no_OfWeek = round(floor($date_diff/(60*60*24)) / 7) + $endDateWeekCnt - $startDateWeekCnt ;
      $t = 16 - $total_no_OfWeek;
      if( $total_no_OfWeek % 2 == 0 ) {
        $content = [
            'chat_id' => $chat_id,
            'text' =>
                "هفته زوج آموزشی ( هفته $total_no_OfWeek)" . "\n" .
                "$t هفته تا پایان این ترم",
            'reply_to_message_id' => $message_id
        ];
        $telegram->sendMessage($content);
      } else {
        $content = [
            'chat_id' => $chat_id,
            'text' =>
                "هفته فرد آموزشی ( هفته $total_no_OfWeek)" . "\n" .
                "$t هفته تا پایان این ترم",
            'reply_to_message_id' => $message_id
        ];
        $telegram->sendMessage($content);
      }
}


// for log of server of texts
$file = '###.txt';
$current = file_get_contents($file);
$current .= date ("Y-m-d H:i:s", time()) . ":\n" . json_encode(json_decode(file_get_contents('php://input')), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
file_put_contents($file, $current);
?>
