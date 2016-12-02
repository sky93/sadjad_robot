<?php


class Constant
{
    public $last_message = null;
    private $user = null;

    public function __construct($database, $Telegram_Data)
    {
        $this->user = $database->select( 'users', '*', [ 'id[=]' => $Telegram_Data->user_id ] );
        $this->last_message = isset($this->user[0]['last_query']) ? $this->user[0]['last_query'] : null;
    }

    public function user($data = null)
    {
        return $data === null ? $this->user[0] : $this->user[0][$data];
    }
}