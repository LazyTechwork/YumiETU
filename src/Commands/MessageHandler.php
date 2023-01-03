<?php

namespace Yumi\Commands;

use Carbon\Carbon;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Yumi\Models\Statistics;
use Yumi\Models\User;

class MessageHandler extends SystemCommand
{
    protected $name = 'genericmessage';
    protected $description = 'Обработка всех сообщений';

    public function execute(): ServerResponse
    {
        /** @var User $user */
        $user = User::query()->where(
            'telegram_id',
            $this->getMessage()->getFrom()->getId()
        )->firstOrCreate(
            ['telegram_id' => $this->getMessage()->getFrom()->getId()]
        );
        Statistics::query()
            ->where('user_id', '=', $user->id)
            ->whereDate('date', Carbon::now()->startOfDay())
            ->firstOrCreate(
                ['user_id' => $user->id, 'date' => Carbon::now()->startOfDay()]
            )->increment('messages');
        return Request::emptyResponse();
    }
}