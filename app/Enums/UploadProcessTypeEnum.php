<?php

namespace App\Enums;

enum UploadProcessTypeEnum: string
{
    case BULK_UPLOAD_REQUEST = '1';
    case UPDATE_REQUEST = '2';

    case DISPATCHED = '3';

    case PENDING_REQUEST = '4';

    case PENDING_ACCEPTANCE = '5';

    case ACCEPTED = '6';

    case ISSUES = '7';

    case DIRECT_TRANSFER_UPLOAD = '8';

    case SYNC_SALES = '9';

    case ALLOCATION = '10';

    public function label(): string
    {
        return match ($this) {
            self::BULK_UPLOAD_REQUEST => 'Bulk Upload Request',
            self::UPDATE_REQUEST => 'Update Request',
            self::DISPATCHED => 'Dispatched',
            self::PENDING_ACCEPTANCE => 'Pending Acceptance',
            self::PENDING_REQUEST => 'Pending Request',
            self::ACCEPTED => 'Accepted',
            self::ISSUES => 'Issues',
            self::DIRECT_TRANSFER_UPLOAD => 'Direct Transfer Upload',
            self::SYNC_SALES => 'Sync Sales',
            self::ALLOCATION => 'Allocation',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BULK_UPLOAD_REQUEST => 'warning',
            self::UPDATE_REQUEST => 'indigo',
            self::DIRECT_TRANSFER_UPLOAD => 'success',
            self::PENDING_ACCEPTANCE => 'warning',
            self::PENDING_REQUEST => 'warning',
            self::ACCEPTED => 'primary',
            self::ISSUES => 'danger',
            self::DISPATCHED => 'primary',
            self::SYNC_SALES => 'primary',
            self::ALLOCATION => 'primary',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::BULK_UPLOAD_REQUEST => 'heroicon-m-lock-open',
            self::UPDATE_REQUEST => 'heroicon-m-arrow-path-rounded-square',
            self::DISPATCHED => 'heroicon-m-check-circle',
            self::PENDING_ACCEPTANCE => 'heroicon-m-truck',
            self::PENDING_REQUEST => 'heroicon-m-truck',
            self::ACCEPTED => 'heroicon-m-check-circle',
            self::ISSUES => 'heroicon-m-x-circle',
            self::DIRECT_TRANSFER_UPLOAD => 'heroicon-m-truck',
            self::SYNC_SALES => 'heroicon-m-truck',
            self::ALLOCATION => 'heroicon-m-truck',
        };
    }
}
