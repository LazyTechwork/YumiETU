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

    public static function createFromCommand(Command $command): self
    {
        /** @var User $user */
        $user = self::query()->where(
            'telegram_id',
            $command->getMessage()->getFrom()->getId()
        )->firstOrCreate(
            [
                'telegram_id' => $command->getMessage()->getFrom()->getId(),
                'first_name' => $command->getMessage()->getFrom()->getFirstName(
                ),
                'last_name' => $command->getMessage()->getFrom()->getLastName()
            ]
        );

        if ($user->first_name !== $command->getMessage()->getFrom()
                ->getFirstName()
        ) {
            $user->first_name = $command->getMessage()->getFrom()->getFirstName(
            );
        }

        if ($user->last_name !== $command->getMessage()->getFrom()->getLastName(
            )
        ) {
            $user->last_name = $command->getMessage()->getFrom()->getLastName();
        }

        if ($user->isDirty(['first_name', 'last_name'])) {
            $user->save();
        }

        return $user;
    }
}