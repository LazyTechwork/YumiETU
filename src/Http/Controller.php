<?php

namespace Yumi\Http;

use Symfony\Component\HttpFoundation\Response;
use Yumi\Application;

interface Controller
{
    public function __invoke(Application $application): Response;
}