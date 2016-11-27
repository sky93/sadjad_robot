<?php


class webHookGet
{
    public $chat_id = null;
    public $text = null;
    public $first_name = null;
    public $last_name = null;
    public $username = null;
    public $message_id = null;
    public $user_id = null;

    public function __construct($telegram)
    {
        $result = $telegram->getData();
        $this->chat_id = $result["message"]["chat"]["id"];
        $this->text = $result["message"]["text"];
        $this->user_id = $result["message"]["from"]["id"];
        $this->first_name = $result["message"]["from"]["first_name"];
        $this->last_name = $result["message"]["from"]["last_name"];
        $this->username = isset($result["message"]["from"]["username"]) ? $result["message"]["from"]["username"] : null;
        $this->message_id = $result["message"]["message_id"];
    }
}
