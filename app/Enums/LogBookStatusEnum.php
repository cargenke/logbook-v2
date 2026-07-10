<?php

namespace App\Enums;

enum LogBookStatusEnum: string
{
    case PENDING = '1';
    case PROCESSING = '2';
    case PENDING_ACCEPTANCE = '3';
    case WITH_ISSUES = '4';
    case ACCEPTED = '5';

    case DISPATCHED = '6';
    case DIRECT_REGISTRATION = '7';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'WIP',
            self::PENDING_ACCEPTANCE => 'P.Acceptance',
            self::WITH_ISSUES => 'With Issues',
            self::ACCEPTED => 'Accepted',
            self::DISPATCHED => 'Dispatched',
            self::DIRECT_REGISTRATION => 'Direct Registration',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PROCESSING => 'indigo',
            self::PENDING_ACCEPTANCE => 'info',
            self::WITH_ISSUES => 'danger',
            self::ACCEPTED => 'green',
            self::DISPATCHED => 'success',
            self::DIRECT_REGISTRATION => 'success',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-m-lock-open',
            self::PROCESSING => 'heroicon-m-arrow-path-rounded-square',
            self::PENDING_ACCEPTANCE => 'heroicon-m-lock-closed',
            self::WITH_ISSUES => 'heroicon-m-lock-closed',
            self::ACCEPTED => 'heroicon-m-check-badge',
            self::DISPATCHED => 'heroicon-m-truck',
            self::DIRECT_REGISTRATION => 'heroicon-m-truck',
        };
    }

    public static function exists(int|string $value): bool
    {
        return self::tryFrom((string) $value) !== null;
    }
}
