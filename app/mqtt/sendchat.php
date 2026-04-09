<?php
function sendMessage($number, $message, $file)
{
    // $url = 'https://app.whacenter.com/api/send';
    $url = 'https://api.whacenter.com/api/send';

    $ch = curl_init($url);

    $data = array(
        'device_id' => '933bbd2c-8931-421c-8432-8e1ba9b3d795',
        // 'device_id' => '1ad076cd-f291-4946-9376-03ae1d744797',
        'number' => $number,
        'message' => $message,
        'file' => $file,
    );

    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Jalankan request dan ambil response
    $response = curl_exec($ch);
    $error = curl_error($ch);

    curl_close($ch);

    // Kembalikan hasil (jika gagal, kembalikan error)
    if ($error) {
        return "CURL Error: " . $error;
    }

    return $response;
}