<?php

namespace App\Http\Traits;

use App\Models\Device;
use App\Models\User;
use App\Models\Notification;

use App\Http\Helpers\SendPushMessage;

trait NotificationTrait
{
    function sendNotification($sender_id, $receiver_ids, $action, $object)
    {
        $sender = User::find($sender_id);
        $receiver_ids = array_values(array_diff($receiver_ids,[$sender_id]));
        if(!empty($receiver_ids)){
            $devices = Device::whereIn('user_id', $receiver_ids)->get()->pluck('device_token')->toArray();
            if ($sender) {
                $message = $this->constructMessage($sender, $action);
                if ($action != 'friend_request')
                    $this->saveToDB($message, $receiver_ids, $action, $object);

                if (!empty($devices)) {
                    SendPushMessage::send($devices, [
                        'title' => $message,
                        'payload' => [
                            'key' => $action,
                            'object' => $object ?? new \stdClass
                        ]
                    ]);
                }
            }
        }
    }

    function constructMessage($sender, $action)
    {
        switch ($action) {
            case "invited_to_activity":
                $msg = $sender->name . " has invited you to join an activity";
                break;
            case "joined_activity":
                $msg = $sender->name . " has joined in an activity, posted by you";
                break;
            case "liked":
                $msg = $sender->name . " liked your post";
                break;
            case "commented":
                $msg = $sender->name . " commented on your post";
                break;
            case "friend_request":
                $msg = $sender->name . " has requested to be your friend";
                break;
            case "event_reminder":
                $msg = $sender->name . " has sent a reminder for an upcoming activity";
                break;
            default:
                $msg = "Test";
                break;
        }
        return $msg;
    }

    function saveToDB($message, $receiver_ids, $action, $object)
    {
        $objects = [];
        foreach ($receiver_ids as $user_id) {
            array_push($objects, [
                'user_id' => $user_id,
                'text' => $message,
                'unique_key' => $action,
                'payload' => !empty($object) ? @json_encode($object) : null
            ]);
        }
        Notification::insert($objects);
    }
}
