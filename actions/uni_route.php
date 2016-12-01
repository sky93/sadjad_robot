<?php
require_once dirname(__FILE__) . '/../autoload.php';
if ($data->text == $keyboard->buttons['go_back']) {
    $database->update("users", ['last_query' => null], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->user_id,
        'text' => "منوی اصلی:",
        'reply_markup' => $keyboard->key_start()
    ]);

} elseif ($data->text != $keyboard->buttons['go_back']) {
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
        
          $steps .= "$d بعد $ee";
          $steps =  str_replace("&nbsp;"," ",$steps);
          $steps = strip_tags($steps,'<b><code><pre><i>');
        }
    $telegram->sendMessage([
        'chat_id' => $data->user_id,
        'text' => "
▫️ مسافت: <code>$distance</code>
▫️ زمان پیشبینی شده: <code>$duration</code>
▫️ مبدا: <code>$start</code>

▫️ از مسیر: <code>$summary</code>
▫️ مسیر پیشنهادی:$steps",
        'parse_mode' => 'HTML',
        'reply_markup' => $keyboard->key_start()
    ]);  
}
else{
        $telegram->sendMessage([
        'chat_id' => $data->user_id,
        'text' => "خطا در دریافت اطلاعات",
        'parse_mode' => 'HTML',
        'reply_markup' => $keyboard->key_start()
    ]);  
}
$database->update("users", ['last_query' => null], ['id' => $data->user_id]);
}
else{
    $database->update("users", ['last_query' => null], ['id' => $data->user_id]);
    $telegram->sendMessage([
        'chat_id' => $data->user_id,
        'text' => "موقعیتتو برام نفرستادی ! دوباره اقدام کن.\nمنوی اصلی :",
        'reply_markup' => $keyboard->key_start()
    ]);  
}