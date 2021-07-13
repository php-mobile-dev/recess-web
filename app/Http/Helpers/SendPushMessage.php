<?php

namespace App\Http\Helpers;


class SendPushMessage
{

	public static $server_key = 'AAAAWbRVDQ0:APA91bGcFtCrFm1MAGmPt3IyUSODyH3SaD0Gdt3kRSt2Cr0lB24CXY3TfPlI-C70Py4QkntSuqDFOLS7T6v0o1Ar2glCAf5mRxNAfvSSmDh0Clqhlvah-ypZ48na-Tyyxl_1q0Y61zAR';
	public static function send($tokens, $payload)
    {
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
		$title = $payload['title'];

        $notification = [
            'title' => $title,
            'sound' => true,
        ];

        $extraNotificationData = ["message" => $notification, "moredata" => $payload['payload']];

        $fcmNotification = [
            'registration_ids' => $tokens, //multple token array
            //'to'        => $tokens[0], //single token
            'notification' => $notification,
            'data' => $extraNotificationData
        ];

        $headers = [
            'Authorization: key=AAAAWbRVDQ0:APA91bEVklTtKVmNbut9SxLyVE8ki5pL4ne0rjL5rwMPM1aBa8D4F7afDXTHjVAsdh4fdslvsQP-KSS_P_jXZJDIefUYjVLoqlkSL1BuBTotHQz8MEbTSRwVEf281GEh2dYGZ246AzOQ',
            'Content-Type: application/json'
        ];



        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}