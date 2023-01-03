<?php

namespace Yumi\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Longman\TelegramBot\Commands\Command;

class User extends Model
{
    public static int $CUSTOM_NAME_LENGTH = 24;
    public $timestamps = true;
    protected $table = 'users';
    protected $guarded = [];

    public function name(): Attribute
    {
        return Attribute::get(
            fn() => $this->custom_name ??
                implode(
                    ' ',
                    array_filter([$this->first_name, $this->last_name],
                        static fn($it) => $it !== null)
                )
        );
    }

    public function telegramMention(): Attribute
    {
        return Attribute::get(
            fn() => sprintf(
                '<a href="tg://user?id=%d">%s</a>',
                $this->telegram_id,
                $this->name
            )
        );
    }

    public static function createFromCommand(Command $command): self
    {
        $firstName = iconv(
            mb_detect_encoding(
                $command->getMessage()->getFrom()->getFirstName()
            ),
            'utf8',
            $command->getMessage()->getFrom()->getFirstName(),
        );
        $lastName = iconv(
            mb_detect_encoding(
                $command->getMessage()->getFrom()->getLastName()
            ),
            'utf8',
            $command->getMessage()->getFrom()->getLastName(),
        );
        /** @var User $user */
        $user = self::query()->where(
            'telegram_id',
            $command->getMessage()->getFrom()->getId()
        )->firstOrCreate();

        $user->fill([
            'first_name' => $firstName,
            'last_name' => $lastName
        ]);

        if ($user->isDirty(['first_name', 'last_name'])) {
            $user->save();
        }

        return $user;
    }
}