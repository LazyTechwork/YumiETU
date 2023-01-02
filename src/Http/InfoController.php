<?php

namespace Yumi\Http;

use Symfony\Component\HttpFoundation\Response;
use Yumi\Application;

class InfoController implements Controller
{
    public function __invoke(Application $application): Response
    {
        return new Response('Test', 200);
    }
}