<?php

function get_bitly_short_url($url,$login,$appkey,$format='json') {
    $connectURL = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).'&format='.$format;
    $obj = json_decode(file_get_contents($connectURL), true);
    return $obj["data"]["url"];
}