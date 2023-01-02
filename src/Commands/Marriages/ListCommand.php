<?php

namespace Yumi\Commands\Marriages;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class ListCommand extends UserCommand
{
    protected $name = 'marriages';
    protected $description = 'Список браков';
    protected $usage = '/marriages';

    public function execute(): ServerResponse
    {
        return Request::sendMessage([
            'chat_id' => $this->getMessage()->getChat()->getId(),
            'text' => 'Список браков пока пуст'
        ]);
    }
}