<?php
/**
 * Author: bavuong0810@gmail.com
 * Date: 06/12/2022
 * Time: 16:01 PM
 */

namespace App\Http\Controllers;

use App\Tasks\TelegramTask;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{
    private $telegramTask;
    public function __construct() {
        $this->telegramTask = new TelegramTask();
    }

    public function updatedActivity()
    {
//        $text = "<b>[Đam Mỹ]</b> Xác nhận chuyển khoản";
//        $this->telegramTask->sendMessage($text);

        $activity = Telegram::getUpdates();
        dd($activity);
    }
}
