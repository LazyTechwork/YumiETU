<?php

namespace Yumi\Commands;

use Yumi\Commands\Administrative\MigrateCommand;
use Yumi\Commands\Marriages\ListCommand;

class Kernel
{
    protected array $userCommands
        = [
            MessageHandler::class,
            ListCommand::class,
            ConnectVkCommand::class,
        ];

    protected array $adminCommands
        = [
            MigrateCommand::class
        ];

    public function commands(): array
    {
        return array_merge($this->userCommands, $this->adminCommands);
    }
}