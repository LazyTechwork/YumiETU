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

    public function informationInMessage(): string
    {
        $marriages = Marriage::query()
            ->where('wife_id', $this->id)
            ->orWhere('husband_id', $this->id)
            ->orderByDesc('married_since')
            ->with(['wife', 'husband'])
            ->get();

        return sprintf(
            "Имя: %s\n"
            ."Дата рождения: %s\n"
            ."Идентификатор: %d\n"
            ."Идентификатор Telegram: %d\n"
            ."Идентификатор ВКонтакте: %s\n"
            ."Акты смены гражданского состояния:\n%s",
            $this->telegramMention,
            $this->birthday ? $this->birthday->format('d.m.Y')
                : 'не установлена',
            $this->id,
            $this->telegram_id,
            $this->vk_id ?? 'не связан',
            $marriages->map(
                static fn(Marriage $marriage) => sprintf(
                    'Брак: %s и %s (рег. %d, %s)',
                    $marriage->husband->telegramMention,
                    $marriage->wife->telegramMention,
                    $marriage->id,
                    $marriage->divorced_since !== null
                        ? sprintf(
                        'с %s по %s, в браке %d дней',
                        $marriage->married_since->format('d.m.Y'),
                        $marriage->divorced_since->format('d.m.Y'),
                        $marriage->divorced_since->diffInDays(
                            $marriage->married_since
                        )
                    )
                        : sprintf(
                        'с %s, в браке %s',
                        $marriage
                            ->married_since->format('d.m.Y'),
                        $marriage->daysSinceMarriage
                    )
                )
            )->join("\n")
        );
    }

    public static function createFromCommand(Command $command): self
    {
        $firstName = to_utf8($command->getMessage()->getFrom()->getFirstName());
        $lastName = to_utf8($command->getMessage()->getFrom()->getLastName());
        /** @var User $user */
        $user = self::query()->where(
            'telegram_id',
            $command->getMessage()->getFrom()->getId()
        )->firstOrCreate([
            'telegram_id' => $command->getMessage()->getFrom()->getId()
        ], [
            'first_name' => $firstName,
            'last_name' => $lastName
        ]);

        $user->fill([
            'first_name' => $firstName,
            'last_name' => $lastName
        ]);

        if ($user->isDirty(['first_name', 'last_name'])) {
            $user->save();
        }

        return $user;
    }

    public function getEncryptedUserId(): string
    {
        return openssl_encrypt(
            $this->id.'_'.$this->telegram_id,
            'aes-128-gcm',
            $_ENV['APP_KEY']
        );
    }

    public static function decryptUser(string $data): User|null
    {
        $user_id = explode(
            '_',
            openssl_encrypt(
                $data,
                'aes-128-gcm',
                $_ENV['APP_KEY']
            ),
            2
        );
        if (count($user_id) < 2 || !is_numeric($user_id[0])
            || !is_numeric(
                $user_id[1]
            )
        ) {
            return null;
        }
        /** @var User $user */
        $user = self::query()->where('id', $user_id[0])->where(
            'telegram_id',
            $user_id[1]
        )->first();
        return $user;
    }
}