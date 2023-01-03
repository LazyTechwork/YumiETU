<?php

namespace Yumi;

use Closure;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Yumi\Commands\Kernel;

class Application
{
    public static self $instance;
    private Telegram $telegram;

    private Logger $logger;

    private RouteCollection $routes;

    private DatabaseMigrationRepository $migrationRepository;
    private Migrator $migrator;
    private Capsule $capsule;

    /**
     * @throws TelegramException
     */
    public function __construct()
    {
        $this->bootLogger();
        $this->bootEnv();
        $this->bootEloquent();
        $this->bootRoutes();
        $this->bootTelegram();
        $this->bootCommands();

        static::$instance = $this;
    }

    private function bootLogger(): void
    {
        $this->logger = new Logger('MAIN');
        $this->logger->pushHandler(
            new StreamHandler(__DIR__.'/../logs/yumi-log.log')
        );
        ErrorHandler::register($this->logger, Level::cases());
    }

    private function bootEnv(): void
    {
        (new Dotenv())->load(__DIR__.'/../.env');
    }

    private function bootEloquent(): void
    {
        $this->capsule = new Capsule();
        $this->capsule->addConnection([
            'driver' => $_ENV['DB_DRIVER'],
            'host' => $_ENV['DB_HOST'],
            'port' => $_ENV['DB_PORT'],
            'database' => $_ENV['DB_DATABASE'],
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);
        $this->capsule->bootEloquent();
        $this->capsule->setAsGlobal();

        $this->migrationRepository = new DatabaseMigrationRepository(
            $this->capsule->getDatabaseManager(), 'migrations'
        );
        $this->migrator = new Migrator(
            $this->migrationRepository, $this->capsule->getDatabaseManager(),
            new Filesystem()
        );
    }

    private function bootRoutes(): void
    {
        $this->routes = new RouteCollection();

        /** @var Closure $router */
        $router = include __DIR__.'/../routes.php';
        $router($this->routes);
    }

    /**
     * @throws TelegramException
     */
    private function bootTelegram(): void
    {
        $this->telegram = new Telegram(
            $_ENV['BOT_API_KEY'], $_ENV['BOT_USERNAME']
        );

        $this->telegram->enableAdmin(229341720);
    }

    private function bootCommands(): void
    {
        $this->getTelegram()->addCommandClasses((new Kernel())->commands());
    }

    /**
     * @return Telegram
     */
    public function getTelegram(): Telegram
    {
        return $this->telegram;
    }

    public function handleRequest(): Response
    {
        $request = Request::createFromGlobals();
        $context = new RequestContext();
        $context->fromRequest($request);

        $matcher = new UrlMatcher($this->routes, $context);

        try {
            $params = $matcher->match($request->getPathInfo());
        } catch (ResourceNotFoundException) {
            return new JsonResponse(
                ['code' => 404, 'message' => 'Not found'],
                404
            );
        }

        return (new $params['controller']())($request);
    }

    /**
     * @return Migrator
     */
    public function getMigrator(): Migrator
    {
        return $this->migrator;
    }

    /**
     * @return DatabaseMigrationRepository
     */
    public function getMigrationRepository(): DatabaseMigrationRepository
    {
        return $this->migrationRepository;
    }

    /**
     * @return Capsule
     */
    public function getCapsule(): Capsule
    {
        return $this->capsule;
    }

    /**
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }
}
