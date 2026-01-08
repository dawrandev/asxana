<?php

namespace App\Services;

use App\Handlers\Telegram\CallbackQueryHandler;
use App\Handlers\Telegram\MessageHandler;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService
{
    public function handle($request)
    {
        try {
            $update = Telegram::getWebhookUpdate();

            if ($update->getCallbackQuery()) {
                app(CallbackQueryHandler::class)->handle($update->getCallbackQuery());
                return;
            }

            if ($update->getMessage()) {
                app(MessageHandler::class)->handle($update->getMessage());
                return;
            }
        } catch (\Exception $e) {
            Log::error('TelegramService handle error: ' . $e->getMessage());
        }
    }
}
