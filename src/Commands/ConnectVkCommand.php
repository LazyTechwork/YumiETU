<?php

namespace Yumi\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use VK\OAuth\VKOAuth;
use VK\OAuth\VKOAuthDisplay;
use VK\OAuth\VKOAuthResponseType;
use Yumi\Models\User;

class ConnectVkCommand extends UserCommand
{
    protected $name = 'vk';
    protected $usage = '/vk';
    protected $description = 'Позволяет прикрепить аккаунт ВКонтакте к профилю в Аниме клубе';
    protected $private_only = true;

    public function execute(): ServerResponse
    {
        $user = User::createFromCommand($this);

        $oauth = new VKOAuth();
        $client_id = $_ENV['VK_CLIENT_ID'];
        $redirect_uri = $_ENV['VK_REDIRECT_URL'];
        $display = VKOAuthDisplay::PAGE;
        $scope = [];
        $state = $user->getEncryptedUserId();

        $authorization_url = $oauth->getAuthorizeUrl(
            VKOAuthResponseType::CODE,
            $client_id,
            $redirect_uri,
            $display,
            $scope,
            $state
        );

        return $this->replyToUser(
            '<a href="'.$authorization_url
            .'">Перейдите по этой ссылке для авторизации через ВКонтакте</a>',
            [
                'parse_mode' => 'HTML'
            ]
        );
    }
}