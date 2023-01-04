<?php

namespace Yumi\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VK\OAuth\VKOAuth;
use Yumi\Models\User;

class VkAuthController implements Controller
{
    public function __invoke(Request $request): Response
    {
        if (!$request->get('code') || !$request->get('state')) {
            return new Response(
                'Передано недостаточно параметров',
                402
            );
        }
        $user = User::decryptUser($request->get('state'));
        if (!$user) {
            return new Response(
                'Невозможно определить авторизуемого пользователя', 402
            );
        }

        $oauth = new VKOAuth();
        $client_id = $_ENV['VK_CLIENT_ID'];
        $client_secret = $_ENV['VK_SECRET'];
        $redirect_uri = $_ENV['VK_REDIRECT_URL'];
        $code = $request->get('code');

        $response = $oauth->getAccessToken(
            $client_id,
            $client_secret,
            $redirect_uri,
            $code
        );

        $user_id = $response['user_id'];
        $user->vk_id = $user_id;
        $user->save();

        return new Response(
            "К аккаунту Аниме клуба %s был прикреплён аккаунт ВКонтакте с идентификатором: %s\nМожете закрыть это окно",
            200
        );
    }
}