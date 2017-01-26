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
    public $latitude = null;
    public $longitude = null;
    public $raw_message = null;
    public $callback_query = false;
    public $callback_query_id = null;

    public function __construct($telegram)
    {
        $result = $telegram->getData();
        $this->raw_message = $result;

        // See if input is a regular message or callback query

        if ( isset( $result['callback_query'] ) ) {  // It's callback query
            // We have a callback query object
            // https://core.telegram.org/bots/api#callbackquery

            $this->callback_query = true;
            $this->chat_id = $result['callback_query']["message"]["chat"]["id"];
            $this->text = $result['callback_query']["data"]; // Data instead of text in callback query
            $this->callback_query_id = $result['callback_query']["id"];
            $this->user_id = $result['callback_query']["message"]["from"]["id"];
            $this->first_name = isset( $result['callback_query']["message"]["from"]["first_name"] ) ? $result['callback_query']["message"]["from"]["first_name"] : null;
            $this->last_name = isset( $result['callback_query']["message"]["from"]["last_name"] ) ? $result['callback_query']["message"]["from"]["last_name"] : null;
            $this->username = isset( $result['callback_query']["message"]["from"]["username"] ) ? $result['callback_query']["message"]["from"]["username"] : null;
            $this->message_id = $result['callback_query']["message"]["message_id"];
            $this->latitude = isset( $result['callback_query']["message"]["location"]["latitude"] ) ? $result['callback_query']["message"]["location"]["latitude"] : null;
            $this->longitude = isset( $result['callback_query']["message"]["location"]["longitude"] ) ? $result['callback_query']["message"]["location"]["longitude"] : null;
        } else {
            // It's normal message
            // https://core.telegram.org/bots/api#message

            $this->chat_id = $result["message"]["chat"]["id"];
            $this->text = $result["message"]["text"];
            $this->user_id = $result["message"]["from"]["id"];
            $this->first_name = isset( $result["message"]["from"]["first_name"] ) ? $result["message"]["from"]["first_name"] : null;
            $this->last_name = isset( $result["message"]["from"]["last_name"] ) ? $result["message"]["from"]["last_name"] : null;
            $this->username = isset( $result["message"]["from"]["username"] ) ? $result["message"]["from"]["username"] : null;
            $this->message_id = $result["message"]["message_id"];
            $this->latitude = isset( $result["message"]["location"]["latitude"] ) ? $result["message"]["location"]["latitude"] : null;
            $this->longitude = isset( $result["message"]["location"]["longitude"] ) ? $result["message"]["location"]["longitude"] : null;
        }

    }
}
