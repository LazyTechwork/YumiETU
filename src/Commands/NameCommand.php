<?php

namespace Yumi\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Yumi\Models\User;

class NameCommand extends UserCommand
{
    protected $name = 'name';
    protected $usage = '/name <имя>';
    protected $description = 'Поменять имя';

    public function execute(): ServerResponse
    {
        if (!$this->getMessage()->getChat()->isSuperGroup()) {
            return $this->replyToChat(
                'Команду можно выполнять только в беседе',
                ['reply_to_message_id' => $this->getMessage()->getMessageId()]
            );
        }
        $name = trim($this->getMessage()->getText(true));
        if (($nameLength = mb_strlen($name)) > User::$CUSTOM_NAME_LENGTH
            || $nameLength < 4
        ) {
            return $this->replyToChat(
                sprintf(
                    'Длина введённого имени должна быть от 4 до %d. Ваше имя могло быть обрезано до %s, однако изменения не применены.',
                    User::$CUSTOM_NAME_LENGTH,
                    mb_substr($name, 0, User::$CUSTOM_NAME_LENGTH)
                ), [
                    'reply_to_message_id' => $this->getMessage()->getMessageId()
                ]
            );
        } elseif ($nameLength === 0) {
            $user = User::createFromCommand($this);
            if ($user->custom_name !== null) {
                $user->custom_name = null;
                $user->save();
            }
            return $this->replyToChat(
                'Ваше имя было удалено', [
                    'reply_to_message_id' => $this->getMessage()->getMessageId()
                ]
            );
        }

        $user = User::createFromCommand($this);
        $user->custom_name = $name;
        $user->save();

        $this->replyToChat(
            Request::setChatAdministratorCustomTitle([
                'chat_id' => $this->getMessage()->getChat()->getId(),
                'user_id' => $this->getMessage()->getFrom()->getId(),
                'custom_name' => mb_substr($name, 0, 16)
            ])->toJson()
        );

        return $this->replyToChat(
            'Ваше имя было изменено на '.$name.'.', [
                'reply_to_message_id' => $this->getMessage()->getMessageId()
            ]
        );
    }
}