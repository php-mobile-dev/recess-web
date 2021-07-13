<?php

namespace App\Http\Helpers;
use Carbon\Carbon;
use Twilio\Rest\Client;

class SendSMS
{
    public static function sendMessage($message, $recipients)
    {
        $account_sid = env("TWILIO_SID");
        $auth_token = env("TWILIO_AUTH_TOKEN");
        $twilio_number = env("TWILIO_NUMBER");
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($recipients, 
                ['from' => $twilio_number, 'body' => $message] );
        return true;
    }
}
