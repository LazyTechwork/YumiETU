<?php

namespace Yumi\Commands;

use Yumi\Commands\Administrative\MigrateCommand;
use Yumi\Commands\Marriages\DivorceCommand;
use Yumi\Commands\Marriages\ListCommand;
use Yumi\Commands\Marriages\MarryCommand;

class Kernel
{
    protected array $userCommands
        = [
            MessageHandler::class,
            ListCommand::class,
            ConnectVkCommand::class,
            StatsCommand::class,
            NameCommand::class,
            MeCommand::class,
        ];

    protected array $adminCommands
        = [
            MigrateCommand::class,
            MarryCommand::class,
            DivorceCommand::class,
            InfoCommand::class,
        ];

    public function commands(): array
    {
        return array_merge($this->userCommands, $this->adminCommands);
    }
}