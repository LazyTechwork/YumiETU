<?php

namespace Yumi\Commands;

use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Yumi\Models\User;

class InfoCommand extends AdminCommand
{
    protected $name = 'info';
    protected $usage = '/info <id>';
    protected $description = 'Получить информацию о человеке';

    public function execute(): ServerResponse
    {
        $id = trim($this->getMessage()->getText(true));
        if (!is_numeric($id)) {
            return $this->replyToChat(
                'Параметр имеет неверный тип',
                [
                    'reply_to_message_id' => $this->getMessage()->getMessageId(
                    ),
                ]
            );
        }

        $user = User::query()->firstWhere('id', (int)$id);

        return $this->replyToChat(
            $user->informationInMessage(),
            [
                'reply_to_message_id' => $this->getMessage()->getMessageId(),
                'parse_mode' => 'HTML'
            ]
        );
    }
}