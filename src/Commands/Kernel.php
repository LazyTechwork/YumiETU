<?php

namespace Yumi\Commands;

use Yumi\Commands\Marriages\ListCommand;

class Kernel
{
    protected array $userCommands
        = [
            ListCommand::class
        ];

    protected array $adminCommands
        = [

        ];

    public function commands(): array
    {
        return array_merge($this->userCommands, $this->adminCommands);
    }
}