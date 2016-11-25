<?php


class keyboard
{
    public $buttons = [
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

    public function key_start()
    {
        return  '{
                   "keyboard": [
                                 [
                                     "' . $this->buttons['my_uni'] . '"
                                 ],
                                 [
                                    "' . $this->buttons['self'] . '",
                                    "' . $this->buttons['user_profile'] . '"
                                 ],
                                 [
                                    "' . $this->buttons['class_places'] . '",
                                    "' . $this->buttons['week'] . '"
                                 ],
                                 [
                                    "' . $this->buttons['calender'] . '",
                                    "' . $this->buttons['map'] . '"
                                 ],
                                 [
                                    "' . $this->buttons['cancel_news'] . '",
                                    "' . $this->buttons['cancel_news'] . '"
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

    public function key_bargh()
    {
        return
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
    }

    public function link_button()
    {
        return
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
    }


    public function key_uni()
    {
        return '{
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
    }
}