<?php

$url = 'https://fcm.googleapis.com/fcm/send';

$headers = [
    'Authorization: key=BOSULV_vcuXl_FPyGkG41TLL6fPD1_EuKeJ-ddLPVzeRHWyhGtIKQy92Udhzg700h2hxR8klTssN_ma-kNDBwwg',
    'Content-Type: application/json'
];

$data = [
    'to' => '',
    'notification' => [
        'title' => 'Тестовый заголовок',
        'body' => 'Тестовый текст уведомления!'
    ]
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, header());
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$result = curl_exec($ch);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($httpCode === 200) {
    echo "Success send notification";
} else {
    echo "Error to send push-notification: $result";
}