<?php

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,'http://stu.sadjad.ac.ir/Interim.php');
curl_setopt($ch,CURLOPT_POST,2);
curl_setopt($ch,CURLOPT_POSTFIELDS,'StID=&UserPassword=');
//curl_setopt($ch,CURLOPT_POSTFIELDS,'StID=&UserPassword=');
//curl_setopt($ch,CURLOPT_POSTFIELDS,'StID=92412180&UserPassword=highclass');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
curl_setopt($ch, CURLOPT_COOKIEFILE, '-');
curl_setopt($ch, CURLOPT_COOKIEJAR, '-');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$headers = array();
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.84 Safari/537.36';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = substr($result, 0, $header_size);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_setopt($ch,CURLOPT_URL,'http://stu.sadjad.ac.ir/strcss/ShowStSchedule.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'e:\cookie.txt');
//curl_setopt($ch, CURLOPT_COOKIEJAR, 'e:\cookie.txt');
// get headers too with this line
//curl_setopt($ch, CURLOPT_HEADER, 1);
$result = curl_exec($ch);
// get cookie
// multi-cookie variant contributed by @Combuster in comments
//preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
//$cookies = array();
//foreach($matches[1] as $item) {
 //   parse_str($item, $cookie);
 //   $cookies = array_merge($cookies, $cookie);
//}

//$ses = $cookies["PHPSESSID"];

$dom = new domDocument;

@$dom->loadHTML($result);
$dom->preserveWhiteSpace = false;
$tables = $dom->getElementsByTagName('table');
//var_dump($tables->item(1));

$rows = $tables->item(1)->getElementsByTagName('tr');

$raw = [];
foreach ($rows as $row) {
        $tds = $row->getElementsByTagName('td');
        foreach ($tds as $td) {
            $raw[] = $td->textContent;
//            var_dump( $td->textContent);
        }
}
//var_dump($raw);

$day = [[],[],[],[],[],[],[],[]];
$day_iterate = -1;
$i = 0;
$hour = 0;
$time = 5;
while(1){


    if ($raw[$i] == 'جمعه') {
        break;
    }
    if ($raw[$i] == 'شنبه' || $raw[$i] == 'یکشنبه' || $raw[$i] == 'دوشنبه' || $raw[$i] == 'سه شنبه' || $raw[$i] == 'چهارشنبه' || $raw[$i] == 'پنجشنبه'){
        $hour = 0;
        $day_iterate++;
        $time = 6;
    } elseif ($raw[$i] == ' ') {
        $time++;
        $hour++;
    } else {
        array_push($day[$day_iterate], [
            'time' => $time,
            'subject' => $raw[$i]
        ]);
//        echo strpos($raw[$i], 'پروژه');
        if (strpos($raw[$i], 'پروژه') === false){
            $time += 2;
            $hour += 2;
        }else{
            $time += 1;
            $hour += 1;
        }



    }
    if ($hour >= 15) {
        $hour = 0;
        $time = 6;
    }

    $i++;
}

header('Content-Type: application/json');
$final_days = [];
$final_days[] = [
        'name_of_week' => 'شنبه',
        'day_of_week' => 0,
        'classes' => $day[0]
    ];
$final_days[] = [
        'name_of_week' => 'یکشنبه',
        'day_of_week' => 1,
        'classes' => $day[1]
    ];
$final_days[] = [
        'name_of_week' => 'دوشنبه',
        'day_of_week' => 2,
        'classes' => $day[2]
    ];
$final_days[] = [
        'name_of_week' => 'سه‌شنبه',
        'day_of_week' => 3,
        'classes' => $day[3]
    ];
$final_days[] = [
        'name_of_week' => 'چهارشنبه',
        'day_of_week' => 4,
        'classes' => $day[4]
    ];
$final_days[] = [
        'name_of_week' => 'پنج‌شنبه',
        'day_of_week' => 5,
        'classes' => $day[5]
    ];

echo json_encode(['status' => 200, 'data' => $final_days], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//echo $result;
?>
