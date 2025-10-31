<?php

namespace App\Tasks;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Http;

class TelegramTask
{
    public function sendMessage($text, $chat_id = '')
    {
        try {
            if ($chat_id == '') {
                $chat_id = env('TELEGRAM_CHANNEL_ID', '');
            }

            if ($chat_id == '-1002290064631') {
                Http::post('https://discordapp.com/api/webhooks/1382216332157911040/FcXTzitRt1p_awrPFyz6OlFoCHfWgODbWh3r7QDVWFTLVLnHOl6oQ10Y8nUM54ZnWccR', [
                    'content' => $text,
                ]);
            } elseif ($chat_id == 'review') {
                Http::post('https://discordapp.com/api/webhooks/1382216332157911040/FcXTzitRt1p_awrPFyz6OlFoCHfWgODbWh3r7QDVWFTLVLnHOl6oQ10Y8nUM54ZnWccR', [
                    'content' => $text,
                ]);
            } else {
                // General notification channel
                Http::post('https://discordapp.com/api/webhooks/1382216052297175092/T2t3jyJwO0ZEpLkQgcMFR9pztkGodfnIo163OCf6SKjN1pQ8Y0aDlsQ2V7db3Ls-rBki', [
                    'content' => $text,
                ]);
            }

            return [
                'success' => true,
            ];

//            return Telegram::sendMessage([
//                'chat_id' => $chat_id,
//                'parse_mode' => 'HTML',
//                'text' => $text
//            ]);
        } catch (\Exception $e) {
            Log::debug(json_encode($e));
            return [
                'success' => false,
                'message' => 'Fail to send message to Telegram.'
            ];
        }
    }
}
