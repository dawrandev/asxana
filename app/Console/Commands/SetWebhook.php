<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class SetWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseUrl = rtrim(env('APP_URL'), '/');
        $webhookUrl = $baseUrl . '/api/telegram/webhook';

        try {
            $response = Telegram::setWebhook(['url' => $webhookUrl]);
            $this->info('Webhook set successfully: ' . json_encode($response));
        } catch (\Exception $e) {
            $this->error('Xatolik: ' . $e->getMessage());
        }
    }
}
