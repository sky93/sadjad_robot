<?php
class keyboard
{
    public $buttons = [
        'self_service' => '🍗 سیستم تغذیه من',
        'student_schedule' => '📒 برنامه درسی من',
        'class_places' => '👣 مکان کلاس من',
        'week'         => '❓ هفته زوج یا فرد!',
        'calender'     => '📅 تقویم آموزشی',
        'location_to_university'     => '🏢📍 مکان فعلی من تا دانشگاه',
        'location_to_sport'     => '⚽️📍 مکان فعلی من تا سالن تربیت بدنی',
        'location'     => '📍 مسیریابی ها',
        'map_spo'      => '📍 مسیریابی تا سالن',
        'send_my_current_location' => '🌇 ارسال مکان کنونی من',
        'cancel_news'  => '😱 اخبار لغو کلاس ها',
        'news'         => '🗞 آخرین خبرهای دانشگاه',
        'internet'     => '📡 حجم اینترنت من',
        'contact_us'   => '✍ تماس با ما',
        'go_back'      => '➡️ بازگشت',
        'save'         => '✅ ذخیره کن',
        'dont_save'    => '❌ ذخیره نکن',
        'self_service_this_week'    => '🍖 منوی این هفته',
        'self_service_credit'       => '💴 موجودی حساب من',
        'acm_news'     => '⌨ اخبار مسابقه‌ی acm',
        'all_news'     => '📻 تمامی خبرها',
    ];

    public function key_start()
    {
        return  '{
                   "keyboard": [
                                 [
                                     "' . $this->buttons['student_schedule'] . '"
                                 ],
                                 [
                                    "' . $this->buttons['week'] . '"
                                 ],
                                 [
                                    "' . $this->buttons['location'] . '"
                                 ],
                                 [
                                    "' . $this->buttons['news'] . '"
                                 ],
                                 [
                                    "' . $this->buttons['internet'] . '",
                                    "' . $this->buttons['self_service'] . '"
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

    public function news()
    {
        return  '{
                   "keyboard": [
                                 [
                                     "' . $this->buttons['cancel_news'] . '"
                                 ],
                                 [
                                     "' . $this->buttons['acm_news'] . '"
                                 ],
                                 [
                                     "' . $this->buttons['all_news'] . '"
                                 ],
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

    public function self_service_main()
    {
        return  '{
                   "keyboard": [
                                 [
                                     "' . $this->buttons['self_service_this_week'] . '",
                                     "' . $this->buttons['self_service_credit'] . '"
                                 ],
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

    public function location_list()
    {
        return  '{
                   "keyboard": [
                                 [
                                     "' . $this->buttons['location_to_university'] . '"
                                 ],
                                 [
                                     "' . $this->buttons['location_to_sport'] . '"
                                 ],
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

    public function send_my_current_location()
    {
        return  '{
                   "keyboard": [
                                 [
                                     {
                                        "text" : "' . $this->buttons['send_my_current_location'] . '",
                                        "request_location" : true
                                     }
                                 ],
                                 [
                                     "' . $this->buttons['go_back'] . '"
                                 ]
                               ],
                               "resize_keyboard" : true,
                               "request" : true,
                               "ForceReply":{
                                   "force_reply" : true
                               }
                }';
    }
}

