<?php

namespace App\Enums;

enum ReadingStatus: string
{
    case Want = 'want';
    case Reading = 'reading';
    case Done = 'done';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Want => '読みたい',
            self::Reading => '読んでいる',
            self::Done => '読了',
            self::Expired => '期限切れ',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Want => 'bg-blue-100 text-blue-700',
            self::Reading => 'bg-yellow-100 text-yellow-700',
            self::Done => 'bg-green-100 text-green-700',
            self::Expired => 'bg-red-100 text-red-700',
        };
    }
}
