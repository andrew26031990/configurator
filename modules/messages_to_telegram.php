<?php
function SendMessageToBot($whatToSend, $image){
    //$botApiToken = '1675277208:AAHjq6IDErlrINsBh0VvHS_o6qCI974ytFg'; // токен бота
    $botApiToken = '6232662696:AAFz93P-ZB6d1C1XxmvAwd8Am2XISR4eQfo'; // токен бота

    $data = [
        'chat_id' => '-1001385649462', // название канала
        'photo' => $image,
        'parse_mode' => 'HTML',
        'caption' => $whatToSend
    ];
    
    
    $resp = file_get_contents("https://api.telegram.org/bot".$botApiToken."/sendPhoto?".http_build_query($data));
    
    //print $resp;
}

function SendMailToBot($whatToSend){
    //$botApiToken = '1675277208:AAHjq6IDErlrINsBh0VvHS_o6qCI974ytFg'; // токен бота
    $botApiToken = '1675277208:AAHjq6IDErlrINsBh0VvHS_o6qCI974ytFg'; // токен бота

    $data = [
        'chat_id' => '-1001385649462', // название канала
        'text' => $whatToSend,
        'parse_mode' => 'HTML'
    ];
    
    
    $resp = file_get_contents("https://api.telegram.org/bot".$botApiToken."/sendMessage?".http_build_query($data));
    
    //print $resp;
}
