<?php

namespace Yumi\Commands\Administrative;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class MigrateCommand extends AdminCommand
{
    protected $name = 'migrate';
    protected $usage = '/migrate';
    protected $description = 'Выполнить миграцию БД';
    private Migrator $migrator;
    private DatabaseMigrationRepository $migrationRepository;

    public function execute(): ServerResponse
    {
        $this->migrator = app()->getMigrator();
        $this->migrationRepository = app()->getMigrationRepository();

        $this->prepareDatabase();
        try {
            $migrations = $this->migrator->run(
                __DIR__.'/../../../database/migrations'
            );
            return Request::sendMessage([
                'chat_id' => $this->getMessage()->getChat()->getId(),
                'text' => sprintf(
                    "База данных успешно обновлена. Миграции (%d):\n%s",
                    count($migrations), implode("\n", $migrations)
                )
            ]);
        } catch (FileNotFoundException $e) {
            return Request::sendMessage([
                'chat_id' => $this->getMessage()->getChat()->getId(),
                'text' => 'База данных не была обновлена. Файл не найден: '
                    .$e->getMessage()
            ]);
        }
    }


    private function prepareDatabase()
    {
        if (!$this->migrationRepository->repositoryExists()) {
            $this->migrationRepository->createRepository();
        }

        if (!$this->migrator->hasRunAnyMigrations()) {
            $this->loadSchemaState();
        }
    }
}