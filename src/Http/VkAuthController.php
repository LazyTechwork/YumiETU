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

        $vk_id = $response['user_id'];
        if (User::query()->where('vk_id', $vk_id)->exists()) {
            return new Response(
                'Данный аккаунт ВКонтакте уже привязан, отвяжите его, чтобы продолжить.',
                200
            );
        }
        $user->vk_id = $vk_id;
        $user->save();

        return new Response(
            sprintf(
                "К аккаунту Аниме клуба %s был прикреплён аккаунт ВКонтакте с идентификатором: %d\nМожете закрыть это окно",
                $user->name,
                $user->vk_id
            ),
            200
        );
    }
}