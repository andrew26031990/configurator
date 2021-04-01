<?php
function SendMessageToBot($whatToSend, $image){
    $botApiToken = '1035569119:AAEBhERfV2__2Lr8gfWM3PA1n5GaUm0hJt4'; // токен бота
    
    $data = [
        'chat_id' => '@myChanel2603', // название канала
        'photo' => $image,
        'parse_mode' => 'HTML',
        'caption' => $whatToSend
    ];
    
    
    $resp = file_get_contents("https://api.telegram.org/bot".$botApiToken."/sendPhoto?".http_build_query($data));
    
    //print $resp;
}

function SendMailToBot($whatToSend){
    $botApiToken = '1447571605:AAHt8PQrsfhmph8zyZ1yIKj6JvZepwWsxYY'; // токен бота
    
    $data = [
        'chat_id' => '@chanelConfigSborkiMeVitya', // название канала
        'text' => $whatToSend
    ];
    
    
    $resp = file_get_contents("https://api.telegram.org/bot".$botApiToken."/sendMessage?".http_build_query($data));
    
    //print $resp;
}
