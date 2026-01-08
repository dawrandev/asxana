<?php

namespace App\Handlers\Telegram;

use Telegram\Bot\Objects\CallbackQuery;

class CallbackQueryHandler
{
    public function handle(CallbackQuery $callbackQuery)
    {
        $data = $callbackQuery->getData();
        $chatId = $callbackQuery->gerMessage()->getChat()->getId();

        // $handlers
    }
}
