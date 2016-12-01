<?php
class keyboard
{
    public $buttons = [
        'self'         => '🍗 سیستم تغذیه من',
        'user_profile' => '📒 برنامه درسی من',
        'class_places' => '👣 مکان کلاس من',
        'week'         => '⁉ ️هفته آموزشی',
        'calender'     => '📅 تقویم آموزشی',
        'map_uni'      => '📍 مسیریابی تا دانشگاه',
        'map_spo'      => '📍 مسیریابی تا سالن',
        'cancel_news'  => '🔴 اخبار لغو کلاس ها',
        'news'         => '🔵 آخرین اخبار دانشگاه',
        'internet'     => '📡 حجم اینترنت من',
        'contact_us'   => '✍ تماس با ما',
        'go_back'      => '➡️ بازگشت',
        'save'         => '✅ ذخیره کن',
        'dont_save'    => '❌ ذخیره نکن',
    ];

    public function key_start()
    {
        return  '{
                   "keyboard": [
                                 [
                                     "' . $this->buttons['user_profile'] . '"
                                 ],
                                 [
                                    "' . $this->buttons['week'] . '",
                                    "' . $this->buttons['calender'] . '"
                                 ],
                                 [
                                    "' . $this->buttons['map_uni'] . '",
                                    "' . $this->buttons['map_spo'] . '"
                                 ],
                                 [
                                    "' . $this->buttons['cancel_news'] . '",
                                    "' . $this->buttons['news'] . '"
                                 ],
                                 [
                                    "' . $this->buttons['internet'] . '",
                                    "' . $this->buttons['self'] . '"
                                 ],
                                 [
                                    "' . $this->buttons['contact_us'] . '"
                                 ]
                              ],
                              "resize_keyboard" : true,
                              "ForceReply":{
                                  "force_reply" : true
                              }
                }';
    }

    public function go_back()
    {
        return  '{
                   "keyboard": [
                                 [
                                     "' . $this->buttons['go_back'] . '"
                                 ]
                               ],
                               "resize_keyboard" : true,
                               "ForceReply":{
                                   "force_reply" : true
                               }
                }';
    }

    public function save_dont_save()
    {
        return  '{
                   "keyboard": [
                                 [
                                     "' . $this->buttons['save'] . '",
                                     "' . $this->buttons['dont_save'] . '"
                                 ]
                               ],
                               "resize_keyboard" : true,
                               "ForceReply":{
                                   "force_reply" : true
                               }
                }';
    }


public function link_button()
    {
        return
            '{"inline_keyboard":[
[
{
    "text":"بیشتر بخوانید ...",
    "url":"https://google.com"
    }]],
    "ForceReply":
    {
     "force_reply" : true
    }
}';
}
}

