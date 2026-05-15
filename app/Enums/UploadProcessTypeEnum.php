<?php

namespace App\Enums;

enum UploadProcessTypeEnum: string
{
    case BULK_UPLOAD_REQUEST = '1';
    case UPDATE_REQUEST = '2';
    case DIRECT_TRANSFER_UPLOAD = '8';


    public function label(): string
    {
        return match ($this) {
            self::BULK_UPLOAD_REQUEST => 'Bulk Upload Request',
            self::UPDATE_REQUEST => 'Update Request',
            self::DIRECT_TRANSFER_UPLOAD => 'Direct Transfer Upload',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BULK_UPLOAD_REQUEST => 'warning',
            self::UPDATE_REQUEST => 'indigo',
            self::DIRECT_TRANSFER_UPLOAD => 'success',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::BULK_UPLOAD_REQUEST => 'heroicon-m-lock-open',
            self::UPDATE_REQUEST => 'heroicon-m-arrow-path-rounded-square',
            self::DIRECT_TRANSFER_UPLOAD => 'heroicon-m-truck',
 
        };
    }
}
