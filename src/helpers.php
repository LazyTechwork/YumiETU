<?php

use Illuminate\Database\Schema\Builder;
use Monolog\Logger;
use Yumi\Application;

if (!function_exists('app')) {
    function app(): Application
    {
        return Application::$instance;
    }
}

if (!function_exists('schema')) {
    function schema(): Builder
    {
        return app()->getCapsule()->getConnection()->getSchemaBuilder();
    }
}

if (!function_exists('logger')) {
    function logger(): Logger
    {
        return app()->getLogger();
    }
}

if (!function_exists('to_utf8')) {
    function to_utf8(string $text): string
    {
        return iconv(
            mb_detect_encoding($text, mb_detect_order(), true),
            "UTF-8",
            $text
        );
    }
}
