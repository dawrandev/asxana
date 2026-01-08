<?php

namespace App\Commands\Telegram;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Start command';

    public function handle()
    {
        try {
            $chatId = $this->getChatId();

            // 1-XABAR (CARD): Salomlashish qismi
            app('telegram')->sendMessage([
                'chat_id' => $chatId,
                'text'    => "Xosh keldińiz!\nHush kelibsiz!\nДобро пожаловать!",
            ]);

            $maintext = "kk Assalauma aleykum! ✅\n\nMazalı taǵamlarımızǵa buyırtpa beriwdi baslaw ushın tómendegi \"Ashıw\" túymesin basıń.\n\nEger sizde qandayda bir soraw bolsa, iltimas, bizlerdiń qollap-quwatlawımızǵa jazıń.\n\n" .
                "Assolomu aleykum! 👋\n\nMazali taomlarimizga buyurtma berishni boshlash uchun quyidagi “Ochish” tugmasini bosing.\n\nAgar sizda biron bir savol bo'lsa, iltimos, bizning qo'llab-quvvatlashimizga yozing.\n\n" .
                "Здравствуйте! 👋\n\nЧтобы заказать наши блюда, перейдите на платформу доставки, нажав на кнопку ниже \"Открыть\".\n\nЕсли у вас возникнут вопросы, напишите нам в поддержку.";

            $webAppUrl = env('WEB_APP_URL');

            app('telegram')->sendMessage([
                'chat_id' => $chatId,
                'text'    => $maintext,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => 'Ashıw / Ochish / Открыть 🚀', 'web_app' => ['url' => $webAppUrl]]
                        ]
                    ]
                ])
            ]);

            return 'ok';
        } catch (\Throwable $th) {
            Log::error('StartCommand handle error: ' . $th->getMessage());
            return 'error';
        }
    }

    public function getChatId()
    {
        if ($this->update->getMessage()) {
            return $this->update->getMessage()->getChat()->getId();
        } elseif ($this->update->getCallbackQuery()) {
            return $this->update->getCallbackQuery()->getMessage()->getChat()->getId();
        }
    }
}
