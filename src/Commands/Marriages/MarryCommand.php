<?php

namespace Yumi\Commands\Marriages;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class MarryCommand extends UserCommand
{
    protected $name = 'marry';
    protected $description = 'Список браков';
    protected $usage = '/marry <пользователь>';

    public function execute(): ServerResponse
    {
        $wife = $this->getMessage()->getText(true);
        $this->replyToChat('<pre>'.$wife.'</pre>', ['parse_mode' => 'HTML']);

        return Request::emptyResponse();
    }
}