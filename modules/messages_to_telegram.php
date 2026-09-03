<?php
require_once __DIR__ . '/../security.php';

/**
 * Отправляет запрос к Telegram Bot API.
 *
 * @param  string               $method
 * @param  array<string,string> $payload
 * @param  string|null          $token
 * @return bool
 */
function telegramRequest($method, array $payload, $token = null)
{
    $token = $token !== null ? $token : (string) config_get('telegram.token');

    if ($token === '' || empty($payload['chat_id'])) {
        error_log('telegram: не настроен token или chat_id');
        return false;
    }

    $context = stream_context_create(array(
        'http' => array(
            'method'        => 'POST',
            'header'        => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content'       => http_build_query($payload),
            'timeout'       => 5,
            'ignore_errors' => true,
        ),
    ));

    $response = @file_get_contents(
        'https://api.telegram.org/bot' . $token . '/' . $method,
        false,
        $context
    );

    if ($response === false) {
        error_log('telegram: запрос ' . $method . ' не выполнен');
        return false;
    }

    $decoded = json_decode($response, true);

    if (!is_array($decoded) || empty($decoded['ok'])) {
        error_log('telegram: ' . $response);
        return false;
    }

    return true;
}

/**
 * @param  string $whatToSend
 * @param  string $image URL картинки
 * @return bool
 */
function SendMessageToBot($whatToSend, $image)
{
    return telegramRequest(
        'sendPhoto',
        array(
            'chat_id' => (string) config_get('telegram.chat_id'),
            'photo'   => $image,
            'caption' => mb_substr($whatToSend, 0, 1024, 'UTF-8'),
        ),
        (string) config_get('telegram.photo_token', config_get('telegram.token'))
    );
}

/**
 * Отправляет заявку в канал обычным текстом.
 *
 * parse_mode намеренно не используется: в сообщение попадают имя и
 * комментарий клиента, и любая угловая скобка ломала бы разметку.
 *
 * @param  string $whatToSend
 * @return bool
 */
function SendMailToBot($whatToSend)
{
    return telegramRequest('sendMessage', array(
        'chat_id' => (string) config_get('telegram.chat_id'),
        'text'    => mb_substr($whatToSend, 0, 4096, 'UTF-8'),
    ));
}
