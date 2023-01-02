<?php

use Illuminate\Database\Schema\Builder;
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
