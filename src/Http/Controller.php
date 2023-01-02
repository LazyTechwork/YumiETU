<?php

namespace Yumi\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface Controller
{
    public function __invoke(Request $request): Response;
}