<?php

namespace Yumi\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class ConnectVkCommand extends UserCommand
{
    protected $name = 'connect vk';
    protected $usage = '/connect vk <id>';

    public function execute(): ServerResponse
    {
        $text = $this->getMessage()->getText(true);
        if (!preg_match('/\d+/', $text)) {
            return $this->replyToChat(
                'ID ВКонтакте может содержать только цифры'
            );
        }
        return $this->replyToChat('За вами закреплён VK ID '.$text);
    }
}