<?php
require_once dirname(__FILE__) . '/../../autoload.php';

if ($constants->last_message === null) {

    $database->update("users", ['last_query' => 'location', 'last_request' => null], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->chat_id,
        'text' =>  "در این قسمت با توجه به مکان فعلی شما، بهترین مسیر (با توجه به فاصله و ترافیک راه) به شما پیشنهاد می‌گردد." . "\n\n" . 'لطفا یک گزینه را انتخاب کنید:',
        'reply_markup' => $keyboard->location_list()
    ]);

} elseif ( $data->text == $keyboard->buttons['go_back'] ) {

    $database->update("users", ['last_query' => null, 'last_request' => null], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->user_id,
        'text' => "منوی اصلی:",
        'reply_markup' => $keyboard->key_start()
    ]);

} elseif (
    $data->text == $keyboard->buttons['location_to_university'] &&
    $constants->user('last_request') === null
) {

    $database->update("users", ['last_query' => 'location', 'last_request' => 'location_to_university'], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->chat_id,
        'text' =>  'لطفا موقعیت خود را برای من ارسال کنید',
        'parse_mode' => 'Markdown',
        'reply_markup' => $keyboard->send_my_current_location()
    ]);

} elseif (
    $constants->user('last_request') === 'location_to_university'
) {

    $database->update("users", ['last_query' => null, 'last_request' => null], ['id' => $data->user_id]);
    $json = file_get_contents("https://maps.googleapis.com/maps/api/directions/json?language=fa&origin=".$data->latitude.",".$data->longitude."&destination=36.341056,59.529520");
    $obj = json_decode($json,1);
    if ($obj['status'] == 'OK'){
        $distance =  $obj['routes'][0]['legs'][0]['distance']['text'];
        $duration =  $obj['routes'][0]['legs'][0]['duration']['text'];

        $start =  $obj['routes'][0]['legs'][0]['start_address'];
        $fin =  $obj['routes'][0]['legs'][0]['end_address'];

        $summary = $obj['routes'][0]['summary'];

        $steps='';
        foreach($obj['routes'][0]['legs'][0]['steps'] as $eee => $eh){
            $d = $eh['distance']['text'];
            $ee = $eh['html_instructions'];

            $steps .= "$d بعد $ee" . "\n\n";
            
        }
        $steps =  str_replace("&nbsp;"," ",$steps);
        $steps = strip_tags($steps,'<b>`<pre><i>');
        $telegram->sendMessage([
            'chat_id' => $data->user_id,
            'text' => 'مسافت: '            . '<code>' . $distance . '</code>' . "\n" .
                      'زمان پیشبینی شده: ' . '<code>' . $duration . '</code>' . "\n" .
                      'مبدا: '             . '<code>' . $start    . '</code>' . "\n" .
                      "\n\n" .
                      'مسیر پیشنهادی: ' . $steps .
                      'در نقشه های گوگل : '. "<a href='https://www.google.com/maps/dir/36.341056,59.529520/$data->latitude,$data->longitude'>اطلاعات بیشتر</a>",
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboard->key_start()
        ]);
    } else {
        $telegram->sendMessage([
            'chat_id' => $data->user_id,
            'text' => "خطا در دریافت اطلاعات",
            'reply_markup' => $keyboard->key_start()
        ]);
    }

}

elseif (
    $data->text == $keyboard->buttons['location_to_sport'] &&
    $constants->user('last_request') === null
) {

    $database->update("users", ['last_query' => 'location', 'last_request' => 'location_to_sport'], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->chat_id,
        'text' =>  'لطفا موقعیت خود را برای من ارسال کنید',
        'parse_mode' => 'Markdown',
        'reply_markup' => $keyboard->send_my_current_location()
    ]);

} elseif (
    $constants->user('last_request') === 'location_to_sport'
) {

    $database->update("users", ['last_query' => null, 'last_request' => null], ['id' => $data->user_id]);
    $json = file_get_contents("https://maps.googleapis.com/maps/api/directions/json?language=fa&origin=".$data->latitude.",".$data->longitude."&destination=36.331729,59.509248");
    $obj = json_decode($json,1);
    if ($obj['status'] == 'OK'){
        $distance =  $obj['routes'][0]['legs'][0]['distance']['text'];
        $duration =  $obj['routes'][0]['legs'][0]['duration']['text'];

        $start =  $obj['routes'][0]['legs'][0]['start_address'];
        $fin =  $obj['routes'][0]['legs'][0]['end_address'];

        $summary = $obj['routes'][0]['summary'];

        $steps='';
        foreach($obj['routes'][0]['legs'][0]['steps'] as $eee => $eh){
            $d = $eh['distance']['text'];
            $ee = $eh['html_instructions'];

            $steps .= "$d بعد $ee" . "\n\n";
            
        }
        $steps =  str_replace("&nbsp;"," ",$steps);
        $steps = strip_tags($steps,'<b>`<pre><i>');
        $telegram->sendMessage([
            'chat_id' => $data->user_id,
            'text' => 'مسافت: '            . '<code>' . $distance . '</code>' . "\n" .
                      'زمان پیشبینی شده: ' . '<code>' . $duration . '</code>' . "\n" .
                      'مبدا: '             . '<code>' . $start    . '</code>' . "\n" .
                      "\n\n" .
                      'مسیر پیشنهادی: ' . $steps .
                      'در نقشه های گوگل : '. "<a href='https://www.google.com/maps/dir/36.331729,59.509248/$data->latitude,$data->longitude'>اطلاعات بیشتر</a>",
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboard->key_start()
        ]);
    } else {
        $telegram->sendMessage([
            'chat_id' => $data->user_id,
            'text' => "خطا در دریافت اطلاعات",
            'reply_markup' => $keyboard->key_start()
        ]);
    }

}