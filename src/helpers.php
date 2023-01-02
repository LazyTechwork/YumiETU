<?php

use Yumi\Application;

if (!function_exists('app')) {
    function app(): Application
    {
        return Application::$instance;
    }
}
