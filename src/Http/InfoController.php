<?php

namespace Yumi\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InfoController implements Controller
{
    public function __invoke(Request $request): Response
    {
        return new Response('Test', 200);
    }
}